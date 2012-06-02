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
            array( 'name, script', 'length', 'max' => 1000 ),
            array( 'advanced, automated, multipleSolutions', 'boolean' ),
            array( 'localizedItems, backgroundInfo, manualInfo, impactInfo, script', 'safe' ),
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
            'script'         => Yii::t('app', 'Script'),
            'advanced'       => Yii::t('app', 'Advanced'),
            'automated'      => Yii::t('app', 'Automated'),
		);
	}
}