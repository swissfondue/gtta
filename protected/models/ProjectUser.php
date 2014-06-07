<?php

/**
 * This is the model class for table "project_users".
 *
 * The followings are the available columns in table "project_users":
 * @property integer $project_id
 * @property integer $user_id
 * @property boolean $admin
 */
class ProjectUser extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectUser the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "project_users";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("project_id, user_id", "required"),
            array("project_id, user_id", "numerical", "integerOnly" => true),
            array("admin", "boolean"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "user" => array(self::BELONGS_TO, "User", "user_id"),
		);
	}
}
