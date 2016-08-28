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
     * @var boolean certificate required.
     */
    public $certificateRequired;

    /**
     * @var string user's new password.
     */
    public $password;
    
    /**
     * @var string user's new password confirmation.
     */
    public $passwordConfirmation;

    /**
     * @var integer session's duration
     */
    public $sessionDuration;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array("email", "email"),
			array("email", "required"),
            array("passwordConfirmation", "compare", "compareAttribute" => "password"),
            array("email, name", "length", "max" => 1000),
            array("sendNotifications, certificateRequired", "boolean"),
            array("sessionDuration", "numerical", "integerOnly" => true),
            array("password", "safe"),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			"email"                => Yii::t("app", "E-mail"),
            "name"                 => Yii::t("app", "Name"),
            "sendNotifications"    => Yii::t("app", "Send Notifications"),
			"password"             => Yii::t("app", "Password"),
            "passwordConfirmation" => Yii::t("app", "Password Confirmation"),
            "certificateRequired"  => Yii::t("app", "Certificate Required"),
            "sessionDuration"      => Yii::t("app", "Session Duration"),
		);
	}
}