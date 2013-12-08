<?php

/**
 * This is the model class for package edit form.
 */
class PackageEditForm extends CFormModel {
	/**
     * @var string package id
     */
    public $id;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("id", "required"),
		);
	}
}