<?php

/**
 * Class NessusMappingVulnUpdateForm
 */
class NessusMappingVulnUpdateForm extends FormModel  {
    /**
     * @var integer $vulnId
     */
    public $vulnId;

    /**
     * @var integer $checkId
     */
    public $checkId;

    /**
     * @var boolean $insertTitle
     */
    public $insertTitle;

    /**
     * @var integer $resultId
     */
    public $resultId;

    /**
     * @var integer $solutionId
     */
    public $solutionId;

    /**
     * @var integer $rating
     */
    public $rating;

    /**
     * @var boolean $active
     */
    public $active;

    /**
     * Nessus mapping vulns form rules
     * @return array
     */
    public function rules() {
        return [
            ["vulnId, active", "required"],
            ["vulnId, checkId, resultId, solutionId", "numerical", "integerOnly" => true],
            ["rating", "in", "range" => TargetCheck::getValidRatings()],
            ["active, insertTitle", "boolean"],
            ["active, insertTitle", "default", "value" => 0],
        ];
    }
}