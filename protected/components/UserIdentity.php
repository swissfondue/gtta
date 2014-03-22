<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {
    /**
     * @var string email
     */
    public $email;
	private $_id;
    
    /**
	 * Constructor.
	 * @param string $email email
	 * @param string $password password
	 */
	public function __construct($email, $password) {
        parent::__construct($email, $password);
		$this->email = $email;
	}

	/**
	 * Authenticate a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		$user = User::model()->findByAttributes(array(
            "email" => $this->email,
        ));
        
		if ($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if ($user->password != hash("sha256", $this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {
			$this->_id = $user->id;
            $this->username = $user->email;
			$this->email = $user->email;
			$this->errorCode = self::ERROR_NONE;

            $entry = new LoginHistory();
            $entry->user_id = $user->id;
            $entry->user_name = $user->name ? $user->name : $user->email;
            $entry->save();
		}
        
		return $this->errorCode == self::ERROR_NONE;
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId() {
		return $this->_id;
	}
}