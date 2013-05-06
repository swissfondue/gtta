<?php

/**
 * This is the model class for settings edit form.
 */
class SettingsEditForm extends LocalizedFormModel
{
    /**
     * @var string timezone
     */
    public $timezone;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('timezone', 'required'),
            array('timezone', 'in', 'range' => array_keys(TimeZones::$zones)),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'timezone' => Yii::t('app', 'Time Zone'),
		);
	}
}