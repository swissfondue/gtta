<?php

/**
 * This is the model class for login form.
 */
class LoginForm extends CFormModel
{
	/**
     * @var string user's email address.
     */
    public $email;
    
    /**
     * @var string user's password.
     */
    public $password;
    
    /**
     * @var UserIdentity stores user's identity.
     */
    private $_identity;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'email, password', 'required' ),
            array( 'email', 'email' ),
            array( 'password', 'authenticate' ),
		);
	}
    
    /**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params)
	{
		$this->_identity = new UserIdentity($this->email, $this->password);
		
        if (!$this->_identity->authenticate())
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Incorrect username or password.'));
            return false;
        }
        
        return true;
	}

	/**
	 * Logs in the user using the given email and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if ($this->_identity === null)
		{
			$this->_identity = new UserIdentity($this->email, $this->password);
			$this->_identity->authenticate();
		}
        
		if($this->_identity->errorCode === UserIdentity::ERROR_NONE)
		{
			Yii::app()->user->login($this->_identity);
			return true;
		}
		else
			return false;
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'email'    => Yii::t('app', 'E-mail'),
			'password' => Yii::t('app', 'Password'),
		);
	}
}