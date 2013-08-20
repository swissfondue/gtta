<?php

/**
 * This is the model class for update form
 */
class UpdateForm extends CFormModel {
	/**
     * @var boolean proceed
     */
    public $proceed;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("proceed", "required"),
		);
	}
}