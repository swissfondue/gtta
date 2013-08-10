<?php

/**
 * This is the model class for project detail edit form.
 */
class ProjectDetailEditForm extends CFormModel
{
	/**
     * @var string subject.
     */
    public $subject;

    /**
     * @var string content.
     */
    public $content;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'subject, content', 'required' ),
            array( 'subject', 'length', 'max' => 1000 ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'subject' => Yii::t('app', 'Subject'),
            'content' => Yii::t('app', 'Content'),
		);
	}
}