<?php

/**
 * This is the model class for target custom check edit form.
 */
class TargetCustomCheckEditForm extends CFormModel {
    /**
     * @var string name.
     */
    public $name;

    /**
     * @var string rating.
     */
    public $rating;

    /**
     * @var string solution.
     */
    public $solution;

    /**
     * @var string solution title.
     */
    public $solutionTitle;

    /**
     * @var integer id.
     */
    public $id;

    /**
     * @var integer control id.
     */
    public $controlId;

    /**
     * @var array attachment titles
     */
    public $attachmentTitles;

    /**
     * @var array fields
     */
    public $fields;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("rating", "in", "range" => TargetCheck::getValidRatings()),
            array("name, solutionTitle", "length", "max" => 1000),
            array("id, controlId", "numerical", "integerOnly" => true),
            array("solution, attachmentTitles, fields", "safe"),
		);
	}
}