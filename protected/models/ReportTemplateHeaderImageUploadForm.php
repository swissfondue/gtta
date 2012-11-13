<?php

/**
 * This is the model class for report tpl header image upload form.
 */
class ReportTemplateHeaderImageUploadForm extends CFormModel
{
	/**
     * @var CUploadedFile image.
     */
    public $image;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'image', 'required' ),
            array( 
                'image',
                'file',
                'maxSize'  => Yii::app()->params['attachments']['maxSize'],
                'maxFiles' => 1,
                'types' => array( 'jpg', 'png' ),
            ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array( 'attachment' => 'Attachment' );
	}
}