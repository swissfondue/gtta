<?php

/**
 * This is the model class for target check update form.
 */
class TargetCheckUpdateForm extends CFormModel {
    /**
     * @var array checks.
     */
    public $checks;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("checks", "safe"),
		);
	}
}