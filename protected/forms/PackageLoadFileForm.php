<?php

/**
 * This is the model class for loading package file form.
 */
class PackageLoadFileForm extends CFormModel {
    /**
     * @var string $path
     */
    public $path;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("path", "required")
        );
    }
}