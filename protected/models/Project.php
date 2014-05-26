<?php

/**
 * This is the model class for table "projects".
 *
 * The followings are the available columns in table "projects":
 * @property integer $id
 * @property integer $client_id
 * @property string $year
 * @property string $name
 * @property string $deadline
 * @property integer $status
 * @property string $vuln_overdue
 * @property boolean $guided_test
 * @property string $start_date
 */
class Project extends ActiveRecord {
    /**
     * Project statuses.
     */
    const STATUS_OPEN = 0;
    const STATUS_IN_PROGRESS = 10;
    const STATUS_ON_HOLD = 20;
    const STATUS_FINISHED = 100;

    // sorting
    const FILTER_SORT_DEADLINE = 1;
    const FILTER_SORT_NAME = 2;
    const FILTER_SORT_CLIENT = 3;
    const FILTER_SORT_STATUS = 4;
    const FILTER_SORT_START_DATE = 5;

    // sorting direction
    const FILTER_SORT_ASCENDING = 1;
    const FILTER_SORT_DESCENDING = 2;

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "projects";
	}

    /**
     * Get valid statuses
     * @return array
     */
    public static function getValidStatuses() {
        return array(
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_ON_HOLD,
            self::STATUS_FINISHED
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name, year", "required"),
            array("name", "length", "max" => 1000),
            array("year", "length", "max" => 4),
            array("guided_test", "boolean"),
            array("status", "in", "range" => self::getValidStatuses()),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"client" => array(self::BELONGS_TO, "Client", "client_id"),
            "details" => array(self::HAS_MANY, "ProjectDetail", "project_id"),
            "users" => array(self::MANY_MANY, "User", "project_users(project_id, user_id)"),
            "project_users" => array(self::HAS_MANY, "ProjectUser", "project_id"),
            "targets" => array(self::HAS_MANY, "Target", "project_id"),
            "modules" => array(self::HAS_MANY, "ProjectGtModule", "project_id"),
            "gtChecks" => array(self::HAS_MANY, "ProjectGtCheck", "project_id"),
		);
	}

    /**
     * Check if user is permitted to access the project.
     */
    public function checkPermission() {
        $user = Yii::app()->user;

        if ($user->role == User::ROLE_ADMIN) {
            return true;
        }

        if (($user->role == User::ROLE_CLIENT && $user->client_id == $this->client_id) || $user->role == User::ROLE_USER) {
            $check = ProjectUser::model()->findByAttributes(array(
                "project_id" => $this->id,
                "user_id" => $user->id
            ));

            if ($check) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is project admin
     */
    public function checkAdmin() {
        if (User::checkRole(User::ROLE_ADMIN)) {
            return true;
        }

        if (User::checkRole(User::ROLE_USER)) {
            $check = ProjectUser::model()->findByAttributes(array(
                "project_id" => $this->id,
                "user_id" => Yii::app()->user->id,
            ));

            if ($check && $check->admin) {
                return true;
            }
        }

        return false;
    }
}
