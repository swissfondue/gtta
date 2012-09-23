<?php

/**
 * This is the model class for user project add form.
 */
class UserProjectAddForm extends CFormModel
{
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
			array( 'projectId', 'required' ),
            array( 'projectId', 'numerical', 'integerOnly' => true ),
            array( 'projectId', 'checkProject' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'projectId' => Yii::t('app', 'Project'),
		);
	}

    /**
	 * Checks if project exists.
	 */
	public function checkProject($attribute, $params)
	{
		$project = Project::model()->findByPk($this->projectId);

        if (!$project)
        {
            $this->addError('projectId', Yii::t('app', 'Project not found.'));
            return false;
        }

        return true;
	}
}