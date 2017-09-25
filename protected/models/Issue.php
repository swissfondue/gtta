<?php

/**
 * This is the model class for table "issues".
 *
 * The followings are the available columns in table 'issues':
 * @property integer $id
 * @property integer $project_id
 * @property integer $check_id
 * @property Project $property
 * @property Check $check
 * @property IssueEvidence[] $evidences
 */
class Issue extends ActiveRecord {

    /**
     * @var int index of not fully filled issue evidence with high high/medium/low risk rating (fake property)
     */
    public $not_filled_ev;

    /**
     * @var string issue name (virtual column)
     */
    public $name;

    /**
     * @var int highest rating (virtual column)
     */
    public $top_rating;

    /**
     * @var int number of affected targets (virtual column)
     */
    public $affected_targets;

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
        return "issues";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["project_id, check_id", "required"],
            ["project_id, check_id", "numerical", "integerOnly" => true],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "project" => [self::BELONGS_TO, "Project", "project_id"],
            "check" => [self::BELONGS_TO, "Check", "check_id"],
            "evidences" => [self::HAS_MANY, "IssueEvidence", "issue_id"],
        ];
    }
}