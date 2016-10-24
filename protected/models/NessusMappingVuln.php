<?php

/**
 * This is the model class for table "nessus_mapping_vulns".
 *
 * The followings are the available columns in table "nessus_mapping_vulns":
 * @property integer $id
 * @property integer $nessus_mapping_id
 * @property integer $nessus_plugin_id
 * @property string  $nessus_rating
 * @property string  $nessus_plugin_name
 * @property integer $check_id
 * @property integer $check_result_id
 * @property integer $check_solution_id
 * @property integer $rating
 */
class NessusMappingVuln extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Reference the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "nessus_mapping_vulns";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["nessus_mapping_id, nessus_plugin_id, nessus_rating, nessus_plugin_name", "required"],
            ["nessus_mapping_id, nessus_plugin_id, check_id, rating, check_result_id", "numerical", "integerOnly" => true],
            ["nessus_rating", "length", "max" => 10],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "mapping" => [self::BELONGS_TO, "NessusMapping", "nessus_mapping_id"],
            "check" => [self::BELONGS_TO, "Check", "check_id"],
            "result" => [self::BELONGS_TO, "CheckResult", "check_result_id"],
            "solution" => [self::BELONGS_TO, "CheckSolution", "check_solution_id"],
        ];
    }
}