<?php

/**
 * This is the model class for account restore form.
 */
class AccountRestoreForm extends CFormModel
{
    /**
     * Scenarios.
     */
    const REQUEST_CODE_SCENARIO = 'request_code';
    const RESET_PASSWORD_SCENARIO = 'reset_password';

    /**
     * @var string email.
     */
    public $email;

    /**
     * @var string password.
     */
    public $password;

    /**
     * @var string password confirmation.
     */
    public $passwordConfirmation;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('email', 'EmailValidator', 'on' => self::REQUEST_CODE_SCENARIO),
			array('email', 'required', 'on' => self::REQUEST_CODE_SCENARIO),
            array('email', 'length', 'max' => 1000, 'on' => self::REQUEST_CODE_SCENARIO),
            array('email', 'checkUser', 'on' => self::REQUEST_CODE_SCENARIO),
            array('password, passwordConfirmation', 'required', 'on' => self::RESET_PASSWORD_SCENARIO),
            array('passwordConfirmation', 'compare', 'compareAttribute' => 'password', 'on' => self::RESET_PASSWORD_SCENARIO),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'email' => Yii::t('app', 'E-mail'),
            'password' => Yii::t('app', 'New Password'),
            'passwordConfirmation' => Yii::t('app', 'Password Confirmation'),
		);
	}

    /**
	 * Checks if user exists.
	 */
	public function checkUser($attribute, $params) {
		$user = User::model()->findByAttributes(array("email" => $this->email));

        if (!$user) {
            $this->addError('email', Yii::t('app', 'User not found.'));
            return false;
        }

        return true;
	}
}