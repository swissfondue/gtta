<?php

/**
 * This is the model class for controlling target custom check attachments.
 */
class TargetCustomCheckAttachmentControlForm extends CFormModel {
    /**
     * @var string path.
     */
    public $path;

    /**
     * @var string operation.
     */
    public $operation;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("path, operation", "required"),
            array("path", "length", "max" => 1000),
		);
	}
}