<?php

/**
 * This is the model class for project GT check edit form.
 */
class ProjectGtCheckEditForm extends CFormModel
{
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
     * @var array inputs.
     */
    public $inputs;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('rating', 'in', 'range' => array(ProjectGtCheck::RATING_NONE, ProjectGtCheck::RATING_HIDDEN, ProjectGtCheck::RATING_INFO, ProjectGtCheck::RATING_LOW_RISK, ProjectGtCheck::RATING_MED_RISK, ProjectGtCheck::RATING_HIGH_RISK)),
            array('port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 65536),
            array('protocol, target', 'length', 'max' => 1000),
            array('inputs, result, solutions', 'safe'),
		);
	}
}