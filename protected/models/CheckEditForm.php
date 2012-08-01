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
     * @var string background info.
     */
    public $backgroundInfo;

    /**
     * @var string hints.
     */
    public $hints;

    /**
     * @var string question.
     */
    public $question;

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
     * @var string protocol.
     */
    public $protocol;

    /**
     * @var integer port.
     */
    public $port;

    /**
     * @var string script.
     */
    public $script;

    /**
     * @var boolean advanced.
     */
    public $advanced;

    /**
     * @var boolean automated.
     */
    public $automated;

    /**
     * @var boolean multiple solutions.
     */
    public $multipleSolutions;

    /**
     * @var integer effort.
     */
    public $effort;

    /**
     * @var integer control id.
     */
    public $controlId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name, referenceId, controlId', 'required' ),
            array( 'name, script, protocol, referenceCode, referenceUrl', 'length', 'max' => 1000 ),
            array( 'port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 1000 ),
            array( 'advanced, automated, multipleSolutions', 'boolean' ),
            array( 'localizedItems, backgroundInfo, hints, question, script', 'safe' ),
            array( 'automated', 'checkScript' ),
            array( 'referenceUrl', 'url', 'defaultScheme' => 'http' ),
            array( 'referenceId, effort', 'numerical', 'integerOnly' => true ),
            array( 'referenceId', 'checkReference' ),
            array( 'controlId', 'checkControl' ),
            array( 'effort', 'in', 'range' => array( 2, 5, 20, 40, 60, 120 ) ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'           => Yii::t('app', 'Name'),
            'backgroundInfo' => Yii::t('app', 'Background Info'),
            'hints     '     => Yii::t('app', 'Hints'),
            'question'       => Yii::t('app', 'Question'),
            'script'         => Yii::t('app', 'Script'),
            'advanced'       => Yii::t('app', 'Advanced'),
            'automated'      => Yii::t('app', 'Automated'),
            'protocol'       => Yii::t('app', 'Protocol'),
            'port'           => Yii::t('app', 'Port'),
            'referenceId'    => Yii::t('app', 'Reference'),
            'referenceCode'  => Yii::t('app', 'Reference Code'),
            'referenceUrl'   => Yii::t('app', 'Reference URL'),
            'effort'         => Yii::t('app', 'Effort'),
		);
	}

    /**
     * Check if script value is set.
     */
    public function checkScript($attribute, $params)
    {
        if ($this->automated && !$this->script)
        {
            $this->addError('script', Yii::t('app', 'Script cannot be blank.'));
            return false;
        }

        return true;
    }

    /**
	 * Checks if reference exists.
	 */
	public function checkReference($attribute, $params)
	{
		$reference = Reference::model()->findByPk($this->referenceId);

        if (!$reference)
        {
            $this->addError('referenceId', Yii::t('app', 'Reference doesn\'t exist.'));
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
            $this->addError('controlId', Yii::t('app', 'Control doesn\'t exist.'));
            return false;
        }

        return true;
	}
}