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
     * @var string background info.
     */
    public $backgroundInfo;

    /**
     * @var string question.
     */
    public $question;

    /**
     * @var string result.
     */
    public $result;

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
     * @var boolean create check.
     */
    public $createCheck;

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
     * @var array fields (create new check mode)
     */
    public $fields;

    /**
     * @var timestamp last modification timestamp.
     */
    public $lastModified;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("rating", "in", "range" => TargetCheck::getValidRatings()),
            array("name, solutionTitle", "length", "max" => 1000),
            array("id, controlId, lastModified", "numerical", "integerOnly" => true),
            array("createCheck", "boolean"),
            array("backgroundInfo, question, result, solution, attachmentTitles, fields", "safe"),
        );
    }
}