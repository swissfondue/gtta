<?php

/**
 * This is the model class for project user add form.
 */
class ProjectUserAddForm extends CFormModel
{
	/**
     * @var integer user id.
     */
    public $userId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'userId', 'required' ),
            array( 'userId', 'numerical', 'integerOnly' => true ),
            array( 'userId', 'checkUser' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'userId' => Yii::t('app', 'User'),
		);
	}

    /**
	 * Checks if user exists.
	 */
	public function checkUser($attribute, $params)
	{
		$user = User::model()->findByPk($this->userId);

        if (!$user)
        {
            $this->addError('userId', Yii::t('app', 'User doesn\\\'t exist.'));
            return false;
        }

        return true;
	}
}