<?php

/**
 * This is the model class for account edit form.
 */
class AccountEditForm extends CFormModel
{
	/**
     * @var string user's email address.
     */
    public $email;

    /**
     * @var string user's name.
     */
    public $name;

    /**
     * @var boolean send email notifications.
     */
    public $sendNotifications;

    /**
     * @var string user's new password.
     */
    public $password;
    
    /**
     * @var string user's new password confirmation.
     */
    public $passwordConfirmation;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'email', 'email' ),
			array( 'email', 'required' ),
            array( 'passwordConfirmation', 'compare', 'compareAttribute' => 'password' ),
            array( 'email, name', 'length', 'max' => 1000 ),
            array( 'sendNotifications', 'boolean' ),
            array( 'password', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'email'                => Yii::t('app', 'E-mail'),
            'name'                 => Yii::t('app', 'Name'),
            'sendNotifications'    => Yii::t('app', 'Send Notifications'),
			'password'             => Yii::t('app', 'Password'),
            'passwordConfirmation' => Yii::t('app', 'Password Confirmation'),
		);
	}
}