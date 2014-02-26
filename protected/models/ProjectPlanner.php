<?php

/**
 * This is the model class for table "project_planner".
 *
 * The followings are the available columns in table "project_planner":
 * @property integer $id
 * @property integer $user_id
 * @property integer $target_id
 * @property integer $check_category_id
 * @property integer $project_id
 * @property integer $gt_module_id
 * @property DateTime $start_date
 * @property DateTime $end_date
 * @property float $finished
 * @property User $user
 * @property TargetCheckCategory $targetCheckCategory
 * @property ProjectGtModule $projectGtModule
 */
class ProjectPlanner extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectPlanner the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "project_planner";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("user_id, start_date, end_date", "required"),
            array("user_id, target_id, check_category_id, project_id, gt_module_id", "numerical", "integerOnly" => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "user" => array(self::BELONGS_TO, "User", "user_id"),
            "targetCheckCategory" => array(self::BELONGS_TO, "TargetCheckCategory", array("target_id", "check_category_id")),
            "projectGtModule" => array(self::BELONGS_TO, "ProjectGtModule", array("project_id", "gt_module_id")),
		);
	}
}