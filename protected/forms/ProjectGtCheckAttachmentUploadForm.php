<?php

/**
 * This is the model class for project GT check attachment upload form.
 */
class ProjectGtCheckAttachmentUploadForm extends CFormModel
{
	/**
     * @var CUploadedFile uploaded file.
     */
    public $attachment;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('attachment', 'required'),
            array( 
                'attachment',
                'file',
                'maxSize'  => Yii::app()->params['attachments']['maxSize'],
                'maxFiles' => 1,
            ),
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'attachment' => Yii::t('app', 'Attachment')
        );
	}
}