<?php

/**
 * This is the model class for reference edit form.
 */
class ReferenceEditForm extends CFormModel
{
	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string url.
     */
    public $url;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name', 'required' ),
            array( 'name, url', 'length', 'max' => 1000 ),
            array( 'url', 'url', 'defaultScheme' => 'http' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('app', 'Name'),
            'url'  => Yii::t('app', 'URL'),
		);
	}
}