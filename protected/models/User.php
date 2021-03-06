<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string $role
 * @property integer $client_id
 * @property string $last_action_time
 * @property boolean $send_notifications
 * @property string $password_reset_code
 * @property string $password_reset_time
 * @property boolean $show_reports
 * @property boolean $show_details
 * @property boolean $certificate_required
 * @property boolean $certificate_serial
 * @property boolean $certificate_issuer
 * @property integer $session_duration
 * @property ProjectPlanner[] $planner
 */
class User extends ActiveRecord {
    /**
     * User roles.
     */
    const ROLE_ADMIN = "admin";
    const ROLE_USER = "user";
    const ROLE_CLIENT = "client";

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "users";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("email, password, role", "required"),
            array("email, password, name, password_reset_code", "length", "max" => 1000),
            array("role", "in", "range" => array(self::ROLE_ADMIN, self::ROLE_USER, self::ROLE_CLIENT)),
            array("session_duration", "numerical", "integerOnly" => true),
            array("client_id, last_action_time, send_notifications, show_details, show_reports, password_reset_time, certificate_required, certificate_serial, certificate_issuer", "safe"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"client" => array(self::BELONGS_TO, "Client",  "client_id"),
            "projects" => array(self::MANY_MANY,  "Project", "project_users(user_id, project_id)"),
            "planner" => array(self::HAS_MANY, "ProjectPlanner",  "user_id"),
            "timeSession" => array(self::HAS_ONE, "ProjectTime",  "user_id", "condition" => "time IS NULL"),
		);
	}

    /**
     * Check role.
     */
    static function checkRole($role) {
        switch ($role) {
            case self::ROLE_ADMIN:
            case self::ROLE_CLIENT:
                return Yii::app()->user->role == $role;

            case self::ROLE_USER:
                return in_array(Yii::app()->user->role, array( self::ROLE_ADMIN, self::ROLE_USER ));
        }

        return false;
    }

    /**
     * Get admin user of system
     * @return CActiveRecord
     */
    public static function getAdmin() {
        return User::model()->findByAttributes(["role" => User::ROLE_ADMIN]);
    }
}
