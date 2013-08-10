<?php

/**
 * This is the model class for restore form.
 */
class RestoreForm extends CFormModel
{
	/**
     * @var CUploadedFile uploaded file.
     */
    public $backup;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'backup', 'required' ),
            array( 
                'backup',
                'file',
                'maxSize'  => Yii::app()->params['backups']['maxSize'],
                'maxFiles' => 1,
                'types'    => array( 'zip' ),
            ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array( 'backup' => 'Backup File' );
	}
}