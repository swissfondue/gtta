<?php

/**
 * This is the model class for project report form.
 */
class ProjectReportForm extends CFormModel
{
    /**
     * @var array target ids.
     */
    public $targetIds;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'targetIds', 'safe' ),
		);
	}
}