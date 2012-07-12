<?php

/**
 * This is the model class for project report form.
 */
class ProjectReportForm extends CFormModel
{
    /**
     * @var integer client id.
     */
    public $clientId;

    /**
     * @var integer project id.
     */
    public $projectId;

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
            array( 'clientId, projectId, targetIds', 'safe' ),
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'clientId'  => Yii::t('app', 'Client'),
			'projectId' => Yii::t('app', 'Project'),
			'targetIds' => Yii::t('app', 'Targets'),
		);
	}
}