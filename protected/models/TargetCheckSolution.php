<?php

/**
 * This is the model class for table "target_check_solutions".
 *
 * The followings are the available columns in table 'target_check_solutions':
 * @property integer $target_check_id
 * @property integer $check_solution_id
 * @property TargetCheck $targetCheck
 */
class TargetCheckSolution extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheckSolution the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "target_check_solutions";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("target_check_id, check_solution_id", "required"),
            array("target_check_id, check_solution_id", "numerical", "integerOnly" => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "targetCheck" => array(self::BELONGS_TO, "TargetCheck", "target_check_id"),
            "solution" => array(self::BELONGS_TO, "CheckSolution", "check_solution_id"),
		);
	}
}
