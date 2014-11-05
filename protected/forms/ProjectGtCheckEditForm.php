<?php

/**
 * This is the model class for project GT check edit form.
 */
class ProjectGtCheckEditForm extends CFormModel {
    const CUSTOM_SOLUTION_IDENTIFIER = "custom";

    /**
     * @var string target.
     */
    public $target;

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
     * @var array attachment titles
     */
    public $attachmentTitles;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("rating", "in", "range" => ProjectGtCheck::getValidRatings()),
            array("port", "numerical", "integerOnly" => true, "min" => 0, "max" => 65536),
            array("protocol, target, solutionTitle", "length", "max" => 1000),
            array("saveSolution", "boolean"),
            array("inputs, result, solutions, solution, attachmentTitles", "safe"),
		);
	}
}