<?php

/**
 * This is the model class for vuln edit form.
 */
class VulnEditForm extends CFormModel
{
    /**
     * @var string deadline.
     */
    public $deadline;

    /**
     * @var integer user id.
     */
    public $userId;

    /**
     * @var string status.
     */
    public $status;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'status', 'required' ),
            array( 'deadline', 'date', 'allowEmpty' => true, 'format' => 'yyyy-MM-dd' ),
            array( 'userId', 'checkUser' ),
            array( 'status', 'in', 'range' => array( TargetCheck::STATUS_VULN_OPEN, TargetCheck::STATUS_VULN_RESOLVED ) ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'deadline' => Yii::t('app', 'Deadline'),
            'status'   => Yii::t('app', 'Status'),
            'userId'   => Yii::t('app', 'User'),
		);
	}

    /**
	 * Checks if user exists.
	 */
	public function checkUser($attribute, $params)
	{
        if (!$this->userId)
            return true;

		$user = User::model()->findByPk($this->userId);

        if (!$user)
        {
            $this->addError('userId', Yii::t('app', 'User not found.'));
            return false;
        }

        // forbid clients to be assigned to particular vulnerability
        if ($user->role == User::ROLE_CLIENT)
        {
            $this->addError('userId', Yii::t('app', 'Invalid user role.'));
            return false;
        }

        return true;
	}
}