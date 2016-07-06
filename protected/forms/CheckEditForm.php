<?php

/**
 * This is the model class for check edit form.
 */
class CheckEditForm extends LocalizedFormModel
{
	/**
     * @var string name.
     */
    public $name;

    /**
     * @var integer reference id.
     */
    public $referenceId;

    /**
     * @var string reference code.
     */
    public $referenceCode;

    /**
     * @var string reference url.
     */
    public $referenceUrl;

    /**
     * @var boolean automated.
     */
    public $automated;

    /**
     * @var boolean multiple solutions.
     */
    public $multipleSolutions;

    /**
     * @var boolean private.
     */
    public $private;

    /**
     * @var integer effort.
     */
    public $effort;

    /**
     * @var integer control id.
     */
    public $controlId;

    /**
     * @varv array fields
     */
    public $fields;

    /**
     * @var array hidden
     */
    public $hidden;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name, referenceId, controlId', 'required' ),
            array( 'name, referenceCode, referenceUrl', 'length', 'max' => 1000 ),
            array( 'automated, multipleSolutions, private', 'boolean' ),
            array( 'localizedItems, hidden', 'safe' ),
            array( 'referenceUrl', 'url', 'defaultScheme' => 'http' ),
            array( 'referenceId, effort', 'numerical', 'integerOnly' => true ),
            array( 'referenceId', 'checkReference' ),
            array( 'controlId', 'checkControl' ),
            array( 'effort', 'in', 'range' => array( 2, 5, 20, 40, 60, 120 ) ),
            array( 'fields', 'checkFields' )
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'           => Yii::t('app', 'Name'),
            'automated'      => Yii::t('app', 'Automated'),
            'protocol'       => Yii::t('app', 'Protocol'),
            'port'           => Yii::t('app', 'Port'),
            'referenceId'    => Yii::t('app', 'Reference'),
            'referenceCode'  => Yii::t('app', 'Reference Code'),
            'referenceUrl'   => Yii::t('app', 'Reference URL'),
            'effort'         => Yii::t('app', 'Effort'),
            'private'        => Yii::t('app', 'Private'),
		);
	}

    /**
	 * Checks if reference exists.
	 */
	public function checkReference($attribute, $params)
	{
		$reference = Reference::model()->findByPk($this->referenceId);

        if (!$reference)
        {
            $this->addError('referenceId', Yii::t('app', 'Reference not found.'));
            return false;
        }

        return true;
	}

    /**
	 * Checks if control exists.
	 */
	public function checkControl($attribute, $params)
	{
		$control = CheckControl::model()->findByPk($this->controlId);

        if (!$control)
        {
            $this->addError('controlId', Yii::t('app', 'Control not found.'));
            return false;
        }

        return true;
	}

    /**
     * Parse fields
     * @param Check $check
     */
    public function parseFields(Check $check) {
        foreach ($check->fields as $f) {
            $l10ns = $f->l10n;

            foreach ($l10ns as $l10n) {
                if (!isset($this->fields[$l10n->language_id])) {
                    $this->fields[$l10n->language_id] = [];
                }

                $this->fields[$l10n->language_id][$f->global->name] = $l10n->value;
            }
        }
    }

    /**
     * Return field value
     * @param $fieldName
     * @param $languageId
     * @return null
     */
    public function getFieldValue($fieldName, $languageId) {
        if (!$this->fields) {
            return null;
        }

        return isset($this->fields[$languageId][$fieldName]) ? $this->fields[$languageId][$fieldName] : null;
    }

    /**
     * Check fields
     * @param $attribute
     * @param $params
     */
    public function checkFields($attribute, $params) {
        if (!$this->fields) {
            return true;
        }

        foreach ($this->fields as $language => $fields) {
            foreach ($fields as $name => $value) {
                $field = GlobalCheckField::model()->findByAttributes([
                    "name" => $name
                ]);

                if ($field->type == GlobalCheckField::TYPE_RADIO && !FieldManager::validateField($field->type, $value)) {
                    $this->addError("fields_" . $name, Yii::t("app", "Invalid JSON."));
                }

                if ($name == GlobalCheckField::FIELD_OVERRIDE_TARGET) {
                    $this->{$attribute}[$language][$name] = trim($value);
                }

                if ($name == GlobalCheckField::FIELD_PORT) {
                    $value = (int) $value;

                    if ($value < 0 || $value > 65536) {
                        $this->addError("fields_" . $name, "Port must be between 0 and 65536");

                        return false;
                    }

                    $this->{$attribute}[$language][$name] = $value;
                }
            }
        }

        return true;
    }
}
