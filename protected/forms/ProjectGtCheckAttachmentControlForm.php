<?php

/**
 * This is the model class for controlling project GT check attachments.
 */
class ProjectGtCheckAttachmentControlForm extends CFormModel
{
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
	public function rules()
	{
		return array(
			array('path, operation', 'required'),
            array('path', 'length', 'max' => 1000),
		);
	}
}