<?php

/**
 * This is the model class for check input edit form.
 */
class CheckInputEditForm extends LocalizedFormModel
{
	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string description.
     */
    public $description;

    /**
     * @var string value.
     */
    public $value;

    /**
     * @var integer sort order.
     */
    public $sortOrder;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name, sortOrder', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'sortOrder', 'numerical', 'integerOnly' => true, 'min' => 0 ),
            array( 'localizedItems, description, value', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'        => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'value'       => Yii::t('app', 'Value'),
            'sortOrder'   => Yii::t('app', 'Sort Order'),
		);
	}
}