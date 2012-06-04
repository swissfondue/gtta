<?php

/**
 * This is the model class for controlling entries.
 */
class EntryControlForm extends CFormModel
{
    /**
     * @var integer id.
     */
    public $id;

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
			array( 'id, operation', 'required' ),
            array( 'id', 'numerical', 'integerOnly' => true, 'min' => 0 ),
		);
	}
}