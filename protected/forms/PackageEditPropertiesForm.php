<?php

/**
 * Class PackageEditPropertiesForm
 */
class PackageEditPropertiesForm extends CFormModel {
    /**
     * @var string $path
     */
    public $timeout;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("timeout", "required"),
            array("timeout", "numerical", "integerOnly" => true, "min" => 0),
        );
    }
}