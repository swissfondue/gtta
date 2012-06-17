<?php

/**
 * This is the model class for project comparison form.
 */
class ProjectComparisonForm extends CFormModel
{
    /**
     * @var array project id.
     */
    public $projectId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'projectId', 'numerical', 'integerOnly' => true ),
		);
	}
}