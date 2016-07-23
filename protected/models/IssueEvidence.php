<?php

/**
 * This is the model class for table "issue_evidences".
 *
 * The followings are the available columns in table 'issue_evidences':
 * @property integer $id
 * @property integer $issue_id
 * @property integer $target_check_id
 * @property TargetCheck $targetCheck
 */
class IssueEvidence extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Check the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "issue_evidences";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["issue_id, target_check_id", "required"],
            ["issue_id, target_check_id", "numerical", "integerOnly" => true],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "issue" => [self::BELONGS_TO, "Issue", "issue_id"],
            "targetCheck" => [self::BELONGS_TO, "TargetCheck", "target_check_id"],
        ];
    }
}