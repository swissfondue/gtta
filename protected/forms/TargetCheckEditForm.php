<?php

/**
 * This is the model class for target check edit form.
 */
class TargetCheckEditForm extends CFormModel {
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
     * @var array inputs.
     */
    public $inputs;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array('rating', 'in', 'range' => array( TargetCheck::RATING_NONE, TargetCheck::RATING_HIDDEN, TargetCheck::RATING_INFO, TargetCheck::RATING_LOW_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_HIGH_RISK)),
            array('port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 65536),
            array('protocol, overrideTarget', 'length', 'max' => 1000),
            array('inputs, result, solutions', 'safe'),
		);
	}
}