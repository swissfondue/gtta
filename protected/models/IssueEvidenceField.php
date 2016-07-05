<?php

/**
 * This is the model class for table "issue_evidence_fields".
 *
 * The followings are the available columns in table 'issue_evidence_fields':
 * @property integer $id
 * @property integer $issue_evidence_id
 * @property integer $target_check_field_id
 * @property integer $value
 * @property integer $hidden
 */
class IssueEvidenceField extends ActiveRecord
{
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
        return "issue_evidence_fields";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["issue_evidence_id, target_check_field_id", "required"],
            ["issue_evidence_id, target_check_field_id", "numerical", "integerOnly" => true],
            ["hidden", "boolean"]
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "issue" => [self::BELONGS_TO, "Issue", "issue_id"],
            "target_check_id" => [self::BELONGS_TO, "TargetCheck", "target_check_id"],
        ];
    }
}