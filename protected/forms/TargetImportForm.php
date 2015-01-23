<?php

/**
 * This is the model class for import target form.
 */
class TargetImportForm extends CFormModel
{
    /**
     * @var CUploadedFile uploaded file.
     */
    public $file;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'file', 'required' ),
            array(
                'file',
                'file',
                'maxFiles' => 1,
                'types'    => array( 'csv', 'nessus' ),
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array( 'file' => 'Import File' );
    }
}