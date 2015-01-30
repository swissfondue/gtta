<?php

/**
 * This is the model class for package edit form.
 */
class PackageEditFilesForm extends CFormModel {
    /**
     * Constants
     */
    const OPERATION_SAVE = "save";
    const OPERATION_DELETE = "delete";

    /**
     * @var string $path
     */
    public $path;

    /**
     * @var string $content
     */
    public $content;

    /**
     * @var string operation
     */
    public $operation;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("path, operation", "required"),
            array("operation", "in", "range" => array(self::OPERATION_SAVE, self::OPERATION_DELETE)),
            array("content", "safe"),
        );
    }
}