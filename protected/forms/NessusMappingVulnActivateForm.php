<?php

/**
 * Class NessusMappingVulnActivateForm
 */
class NessusMappingVulnActivateForm extends FormModel  {
    /**
     * @var array $mappingIds
     */
    public $mappingIds;

    /**
     * @var boolean $activate
     */
    public $activate;

    /**
     * Nessus mapping vulns form rules
     * @return array
     */
    public function rules() {
        return [
            ["mappingIds", "required"],
            ["mappingIds", "checkMappingIds"],
            ["activate", "boolean"],
            ["activate", "default", "value" => true]
        ];
    }

    /**
     * Validate mapping ids
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkMappingIds($attribute, $params) {
        try {
            $this->{$attribute} = json_decode($this->{$attribute});
        } catch (Exception $e) {
            $this->addError($attribute, "Invalid ids list");

            return false;
        }

        return true;
    }
}