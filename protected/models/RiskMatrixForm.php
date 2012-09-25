<?php

/**
 * This is the model class for risk matrix form.
 */
class RiskMatrixForm extends CFormModel
{
    /**
     * @var integer template id.
     */
    public $templateId;

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
     * @var array matrix.
     */
    public $matrix;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'templateId, clientId, projectId', 'required' ),
            array( 'templateId, clientId, projectId', 'numerical', 'integerOnly' => true ),
            array( 'targetIds, matrix', 'safe' ),
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