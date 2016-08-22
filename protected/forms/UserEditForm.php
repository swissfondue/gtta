<?php

/**
 * This is the model class for user edit form.
 */
class UserEditForm extends CFormModel
{
    /**
     * Scenarios.
     */
    const ADD_USER_SCENARIO  = "add_user";
    const EDIT_USER_SCENARIO = "edit_user";

	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string email.
     */
    public $email;

    /**
     * @var boolean send email notifications.
     */
    public $sendNotifications;

    /**
     * @var string password.
     */
    public $password;

    /**
     * @var string password confirmation.
     */
    public $passwordConfirmation;

    /**
     * @var string role.
     */
    public $role;

    /**
     * @var integer client id.
     */
    public $clientId;

    /**
     * @var boolean show reports.
     */
    public $showReports;

    /**
     * @var boolean show details.
     */
    public $showDetails;

    /**
     * @var boolean certificate required.
     */
    public $certificateRequired;

    /**
     * @var integer session"s duration
     */
    public $sessionDuration;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array("email", "EmailValidator"),
			array("email, role", "required"),
            array("name, email", "length", "max" => 1000 ),
            array("password, passwordConfirmation", "required", "on" => self::ADD_USER_SCENARIO ),
            array("passwordConfirmation", "compare", "compareAttribute" => "password"),
            array("password", "safe", "on" => self::EDIT_USER_SCENARIO ),
            array("role", "in", "range" => array( User::ROLE_ADMIN, User::ROLE_CLIENT, User::ROLE_USER ) ),
            array("sendNotifications, showReports, showDetails, certificateRequired", "boolean"),
            array("sessionDuration", "numerical", "integerOnly" => true),
            array("clientId", "checkClient"),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			"name"                 => Yii::t("app", "Name"),
            "email"                => Yii::t("app", "E-mail"),
            "password"             => Yii::t("app", "Password"),
            "passwordConfirmation" => Yii::t("app", "Password Confirmation"),
            "sendNotifications"    => Yii::t("app", "Send Notifications"),
            "role"                 => Yii::t("app", "Role"),
            "clientId"             => Yii::t("app", "Client"),
            "showReports"          => Yii::t("app", "Show Reports"),
            "showDetails"          => Yii::t("app", "Show Details"),
            "certificateRequired"  => Yii::t("app", "Certificate Required"),
		);
	}

    /**
	 * Checks if client exists.
	 */
	public function checkClient($attribute, $params)
	{
        if ($this->role != User::ROLE_CLIENT)
            return true;

		$client = Client::model()->findByPk($this->clientId);

        if (!$client)
        {
            $this->addError("clientId", Yii::t("app", "Client not found."));
            return false;
        }

        return true;
	}
}