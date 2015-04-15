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
            array( 'name, protocol, referenceCode, referenceUrl', 'length', 'max' => 1000 ),
            array( 'port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 1000 ),
            array( 'automated, multipleSolutions', 'boolean' ),
            array( 'localizedItems, backgroundInfo, hints, question', 'safe' ),
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
}