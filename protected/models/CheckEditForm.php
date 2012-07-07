<?php

/**
 * This is the model class for check edit form.
 */
class CheckEditForm extends CFormModel
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
     * @var string impact info.
     */
    public $impactInfo;

    /**
     * @var string manual info.
     */
    public $manualInfo;

    /**
     * @var string reference.
     */
    public $reference;

    /**
     * @var string question.
     */
    public $question;

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
     * @var array localized items.
     */
    public $localizedItems;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name', 'required' ),
            array( 'name, script, protocol', 'length', 'max' => 1000 ),
            array( 'port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 1000 ),
            array( 'advanced, automated, multipleSolutions', 'boolean' ),
            array( 'localizedItems, backgroundInfo, manualInfo, impactInfo, reference, question, script', 'safe' ),
            array( 'automated', 'checkScript' ),
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
            'impactInfo'     => Yii::t('app', 'Impact Info'),
            'manualInfo'     => Yii::t('app', 'Manual Info'),
            'reference'      => Yii::t('app', 'Reference'),
            'question'       => Yii::t('app', 'Question'),
            'script'         => Yii::t('app', 'Script'),
            'advanced'       => Yii::t('app', 'Advanced'),
            'automated'      => Yii::t('app', 'Automated'),
            'protocol'       => Yii::t('app', 'Protocol'),
            'port'           => Yii::t('app', 'Port'),
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
}