<?php

/**
 * This is the model class for project edit form.
 */
class ProjectEditForm extends CFormModel {
    /**
     * Scenarios.
     */
    const ADMIN_SCENARIO = "admin";
    const USER_SCENARIO = "user";

	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string year.
     */
    public $year;

    /**
     * @var string start date.
     */
    public $startDate;

    /**
     * @var string deadline.
     */
    public $deadline;

    /**
     * @var string status.
     */
    public $status;

    /**
     * @var integer client id.
     */
    public $clientId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("name, year, deadline, status, clientId", "required", "on" => self::ADMIN_SCENARIO),
            array("status", "required", "on" => self::USER_SCENARIO),
            array("name", "length", "max" => 1000),
            array("year", "length", "max" => 4, "min" => 4),
            array("year", "match", "pattern" => '/^\d{4}$/'),
            array("deadline, startDate", "date", "allowEmpty" => false, "format" => "yyyy-MM-dd"),
            array("clientId", "checkClient"),
            array("status", "in", "range" => Project::getValidStatuses()),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			"name" => Yii::t("app", "Name"),
            "year" => Yii::t("app", "E-mail"),
            "startDate" => Yii::t("app", "Start Date"),
            "deadline" => Yii::t("app", "Deadline"),
            "status" => Yii::t("app", "Status"),
            "clientId" => Yii::t("app", "Client"),
		);
	}

    /**
	 * Checks if client exists.
	 */
	public function checkClient($attribute, $params) {
		$client = Client::model()->findByPk($this->clientId);

        if (!$client) {
            $this->addError("clientId", Yii::t("app", "Client not found."));
            return false;
        }

        return true;
	}
}