<?php

/**
 * This is the model class for project user edit form.
 */
class ProjectUserEditForm extends CFormModel {
    /**
     * Scenarios.
     */
    const NEW_SCENARIO = "new";
    const SAVE_SCENARIO = "save";

	/**
     * @var integer user id.
     */
    public $userId;

    /**
     * @var boolean admin.
     */
    public $admin;

    /**
     * @var float hours allocated.
     */
    public $hoursAllocated;

    /**
     * @var float hours spent.
     */
    public $hoursSpent;

    /**
     * @var integer project id.
     */
    private $_projectId;

    /**
     * Constructor
     * @param string $scenario
     * @param null $projectId
     */
    public function __construct($scenario, $projectId=null) {
        parent::__construct($scenario);
        $this->_projectId = $projectId;
    }

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("userId", "required", "on" => self::NEW_SCENARIO),
            array("userId", "numerical", "integerOnly" => true, "on" => self::NEW_SCENARIO),
            array("userId", "checkUser"),
            array("admin", "boolean"),
            array("hoursAllocated, hoursSpent", "numerical", "min" => 0),
            array("hoursAllocated", "checkHours"),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			"userId" => Yii::t("app", "User"),
            "admin" => Yii::t("app", "Admin"),
            "hoursAllocated" => Yii::t("app", "Hours Allocated"),
            "hoursSpent" => Yii::t("app", "Hours Spent"),
		);
	}

    /**
	 * Checks if user exists.
	 */
	public function checkUser($attribute, $params) {
		$user = User::model()->findByPk($this->userId);

        if (!$user) {
            $this->addError("userId", Yii::t("app", "User not found."));
            return false;
        }

        if ($user->role == User::ROLE_CLIENT && $this->admin) {
            $this->addError("admin", Yii::t("app", "Client can\"t be a project admin."));
            return false;
        }

        $project = Project::model()->findByPk($this->_projectId);

        if (!$project) {
            return true;
        }

        if ($user->role == User::ROLE_CLIENT && $user->client_id != $project->client_id) {
            $this->addError("userId", Yii::t("app", "User belongs to another client."));
        }

        return true;
	}

    /**
	 * Checks if user has a valid value for hours allocated.
	 */
	public function checkHours($attribute, $params) {
        if (!$this->_projectId) {
            return true;
        }

		$project = Project::model()->with("userHoursAllocated")->findByPk($this->_projectId);

        if (!$project) {
            return true;
        }

        $user = ProjectUser::model()->findByAttributes(array(
            "project_id" => $project->id,
            "user_id" => $this->userId,
        ));

        if (!$user) {
            return true;
        }

        $available = $project->hours_allocated - $project->userHoursAllocated + $user->hours_allocated;

        if ($this->hoursAllocated > $available) {
            $this->addError(
                "hoursAllocated",
                Yii::t("app", "Hours allocated for the user can't be more than {hours}.", array(
                    "{hours}" => sprintf("%.1f", $available),
                ))
            );

            return false;
        }

        return true;
	}
}