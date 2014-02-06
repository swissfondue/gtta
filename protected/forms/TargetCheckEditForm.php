<?php

/**
 * This is the model class for target check edit form.
 */
class TargetCheckEditForm extends CFormModel {
    const CUSTOM_SOLUTION_IDENTIFIER = "custom";

    /**
     * @var string override target.
     */
    public $overrideTarget;

    /**
     * @var string protocol.
     */
    public $protocol;

    /**
     * @var integer port.
     */
    public $port;

	/**
     * @var string result.
     */
    public $result;

    /**
     * @var string rating.
     */
    public $rating;

    /**
     * @var array solutions.
     */
    public $solutions;

    /**
     * @var string solution.
     */
    public $solution;

    /**
     * @var string solution title.
     */
    public $solutionTitle;

    /**
     * @var boolean save solution.
     */
    public $saveSolution;

    /**
     * @var array inputs.
     */
    public $inputs;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("rating", "in", "range" => TargetCheck::getValidRatings()),
            array("port", "numerical", "integerOnly" => true, "min" => 0, "max" => 65536),
            array("protocol, overrideTarget, solutionTitle", "length", "max" => 1000),
            array("saveSolution", "boolean"),
            array("inputs, result, solutions, solution", "safe"),
		);
	}
}