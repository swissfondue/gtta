<?php

/**
 * This is the model class for target check category edit form.
 */
class TargetCheckCategoryEditForm extends CFormModel
{
	/**
     * @var boolean advanced.
     */
    public $advanced;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'advanced', 'required' ),
            array( 'advanced', 'boolean' ),
		);
	}
}