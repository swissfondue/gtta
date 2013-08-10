<?php

/**
 * This is the model class for project select form.
 */
class ProjectSelectForm extends CFormModel
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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'clientId, projectId', 'required' ),
            array( 'clientId, projectId', 'numerical', 'integerOnly' => true ),
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
		);
	}
}