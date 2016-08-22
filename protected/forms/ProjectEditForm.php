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
     * @var float hours allocated.
     */
    public $hoursAllocated;

    /**
     * @var integer project id.
     */
    private $_projectId;

    /**
     * @var array hidden fields
     */
    public $hiddenFields;

    /**
     * @var language id
     */
    public $languageId;

    /**
     * Constructor
     * @param string $scenario
     * @param null $projectId
     */
    public function __construct($scenario="", $projectId=null) {
        parent::__construct($scenario);
        $this->_projectId = $projectId;
        $this->hoursAllocated = 0;
    }

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("name, year, deadline, status, clientId, hoursAllocated", "required", "on" => self::ADMIN_SCENARIO),
            array("status", "required", "on" => self::USER_SCENARIO),
            array("name", "length", "max" => 1000),
            array("year", "length", "max" => 4, "min" => 4),
            array("year", "match", "pattern" => '/^\d{4}$/'),
            array("deadline, startDate", "date", "allowEmpty" => false, "format" => "yyyy-MM-dd"),
            array("clientId", "checkClient"),
            array("status", "in", "range" => Project::getValidStatuses()),
            array("hoursAllocated", "numerical", "min" => 0),
            array("languageId", "numerical", "integerOnly" => true),
            array("languageId", "checkLanguage"),
            array("hoursAllocated", "checkHours"),
            array("hiddenFields", "safe"),
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
            "hoursAllocated" => Yii::t("app", "Hours Allocated"),
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

    /**
	 * Checks if user has provided a valid value for hours allocated.
	 */
	public function checkHours($attribute, $params) {
        if (!$this->_projectId) {
            return true;
        }

		$project = Project::model()->with("userHoursAllocated")->findByPk($this->_projectId);

        if (!$project) {
            return true;
        }

        if ($this->hoursAllocated < $project->userHoursAllocated) {
            $this->addError(
                "hoursAllocated",
                Yii::t("app", "Hours allocated for this project can't be less than {hours}.", array(
                    "{hours}" => sprintf("%.1f", $project->userHoursAllocated),
                ))
            );

            return false;
        }

        return true;
	}

    /**
     * Check if language with such id exists in database
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkLanguage($attribute, $params) {
        if (!$this->languageId) {
            $this->languageId = null;
            return true;
        }

        $language = Language::model()->findByPk($this->languageId);

        if (!$language) {
            $this->addError("languageId", "Language not exists.");
            return false;
        }

        return true;
    }
}