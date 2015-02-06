<?php
/**
 * This is the model class for table "target_checks".
 * @property integer target_check_id
 * @property integer check_script_id
 * @property boolean start
 * @property integer timeout
 */
class TargetCheckScript extends ActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TargetCheck the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "target_check_scripts";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("target_check_id, check_script_id", "required"),
            array("target_check_id, check_script_id, timeout", "numerical", "integerOnly" => true),
            array("start", "boolean"),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "check" => array(self::BELONGS_TO, "TargetCheck", "target_check_id"),
            "script" => array(self::BELONGS_TO, "CheckScript", "check_script_id"),
        );
    }
}