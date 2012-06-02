<?php

/**
 * This is the model class for target check edit form.
 */
class TargetCheckEditForm extends CFormModel
{
	/**
     * @var string result.
     */
    public $result;

    /**
     * @var integer rating.
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
			array( 'rating', 'required' ),
            array( 'inputs, result, solutions', 'safe' ),
		);
	}
}