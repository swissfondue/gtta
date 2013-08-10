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
     * @var boolean admin.
     */
    public $admin;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'userId, admin', 'required' ),
            array( 'userId', 'numerical', 'integerOnly' => true ),
            array( 'admin', 'boolean' ),
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
            'admin'  => Yii::t('app', 'Admin'),
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
            $this->addError('userId', Yii::t('app', 'User not found.'));
            return false;
        }

        if ($user->role == User::ROLE_CLIENT && $this->admin)
        {
            $this->addError('admin', Yii::t('app', 'Client can\'t be a project admin.'));
            return false;
        }

        return true;
	}
}