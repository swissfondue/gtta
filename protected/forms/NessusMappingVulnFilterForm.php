<?php

/**
 * Class NessusMappingVulnFilterForm
 */
class NessusMappingVulnFilterForm extends FormModel  {
    /**
     * @var integer $mappingId
     */
    public $mappingId;

    /**
     * @var array $hosts
     */
    public $hosts;

    /**
     * @var array $ratings
     */
    public $ratings;

    /**
     * sort variable
     *
     * @var $sortBy
     */
    public $sortBy;

    /**
     * sort direction
     *
     * @var $sortDirection
     */
    public $sortDirection;

    /**
     * Nessus mapping vulns form rules
     * @return array
     */
    public function rules() {
        return [
            [["sortBy", "sortDirection"],'safe'],
            ["mappingId", "required"],
            ["mappingId", "numerical", "integerOnly" => true],
            ["mappingId", "checkMapping"],
            ["hosts", "checkHosts"],
            ["ratings", "checkRatings"],
        ];
    }

    /**
     * Validate hosts
     * @param $attribute
     * @param $params
     */
    public function checkHosts($attribute, $params) {
        try {
            $hosts = json_decode($this->{$attribute});
            $this->{$attribute} = $hosts;
        } catch (Exception $e) {
            $this->addError($attribute, "Invalid host list.");
            return false;
        }

        return true;
    }

    /**
     * Validate ratings
     * @param $attribute
     * @param $params
     */
    public function checkRatings($attribute, $params) {
        try {
            $ratings = json_decode($this->{$attribute});
            $this->{$attribute} = $ratings;
        } catch (Exception $e) {
            $this->addError($attribute, "Invalid rating list.");
            return false;
        }

        return true;
    }

    /**
     * Validate mapping id
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkMapping($attribute, $params) {
        $mapping = NessusMapping::model()->findByPk($this->{$attribute});

        if (!$mapping) {
            $this->addError($attribute, "Mapping not exists.");
            return false;
        }

        return true;
    }
}