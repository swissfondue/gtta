<?php

/**
 * This is the model class for risk category edit form.
 */
class RiskCategoryEditForm extends CFormModel
{
	/**
     * @var string name.
     */
    public $name;

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
            array( 'name', 'length', 'max' => 1000 ),
            array( 'localizedItems', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('app', 'Name'),
		);
	}
}