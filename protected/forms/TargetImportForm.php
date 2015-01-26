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
     * @var string type
     */
    public $type;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'file, type', 'required' ),
            array(
                'file',
                'file',
                'maxFiles' => 1,
                'types'    => array_keys(ImportManager::$types),
            ),
            array("type", "in", "range" => array_keys(ImportManager::$types))
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array( 'file' => 'File' );
        return array( 'type' => 'Type' );
    }
}