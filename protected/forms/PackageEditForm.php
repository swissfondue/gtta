<?php

/**
 * This is the model class for package edit form.
 */
class PackageEditForm extends CFormModel {
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
            array("operation", "in", "range" => array("save", "delete")),
            array("content", "safe"),
        );
    }
}