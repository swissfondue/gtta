<?php

/**
 * This is the model class for check script edit form.
 */
class CheckScriptEditForm extends LocalizedFormModel
{
	/**
     * @var string name.
     */
    public $name;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
            array('name', 'length', 'max' => 1000),
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