<?php

/**
 * Class NessusMappingEditForm
 */
class NessusMappingEditForm extends LocalizedFormModel {
    /**
     * @var string $name
     */
    public $name;

    /**
     * Nessus mapping form rules
     * @return array
     */
    public function rules() {
        return [
            ["name", "required"],
            ["name", "length", "max" => 1000],
            ["localizedItems", "safe"],
        ];
    }
}