<?php

/**
 * This is the model class for project comparison form.
 */
class ProjectComparisonForm extends CFormModel
{
    /**
     * @var integer client id.
     */
    public $clientId;

    /**
     * @var integer first project id.
     */
    public $projectId1;

    /**
     * @var integer second project id.
     */
    public $projectId2;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'clientId, projectId1, projectId2', 'safe' ),
		);
	}
}