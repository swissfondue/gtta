<?php

/**
 * This is the model class for project GT form.
 */
class ProjectGtForm extends LocalizedFormModel
{
    /**
     * @var array modules.
     */
    public $modules;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('modules', 'safe'),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'modules' => Yii::t('app', 'Modules'),
		);
	}
}