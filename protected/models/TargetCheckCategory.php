<?php

/**
 * This is the model class for table "target_check_categories".
 *
 * The followings are the available columns in table "target_check_categories":
 * @property integer $target_id
 * @property integer $check_category_id
 * @property boolean $advanced
 * @property integer $check_count
 * @property integer $finished_count
 * @property integer $low_risk_count
 * @property integer $med_risk_count
 * @property integer $high_risk_count
 * @property integer $info_count
 * @property boolean $checklist_template
 * @property integer $template_count
 * @property CheckCategory $category
 * @property Target $target
 */
class TargetCheckCategory extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheckCategory the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "target_check_categories";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("target_id, check_category_id", "required"),
            array("target_id, check_category_id", "numerical", "integerOnly" => true),
            array("template_count", "numerical", "min" => 0, "integerOnly" => true),
            array("advanced", "boolean"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "target" => array(self::BELONGS_TO, "Target", "target_id"),
            "category" => array(self::BELONGS_TO, "CheckCategory", "check_category_id"),
		);
	}
}
