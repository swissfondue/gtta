<?php

/**
 * Class NessusMappingVulnsEditForm
 */
class NessusMappingVulnsEditForm extends FormModel  {
    /**
     * @var integer $checkId
     */
    public $checkId;

    /**
     * @var string $pluginTitle
     */
    public $pluginTitle;

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
     * Nessus mapping vulns form rules
     * @return array
     */
    public function rules() {
        return [
            ["checkId", "required"],
            ["checkId, resultId, solutionId", "numerical", "integerOnly" => true],
            ["pluginTitle", "boolean", "default" => false],
            ["rating", "in", "range" => TargetCheck::getValidRatings()]
        ];
    }
}