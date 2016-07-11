<?php

/**
 * This is the model class for table "issue_evidence_solutions".
 *
 * The followings are the available columns in table 'issue_evidence_solutions':
 * @property integer $issue_evidence_id
 * @property integer $check_solution_id
 * @property IssueEvidence $evidence
 */
class IssueEvidenceSolution extends ActiveRecord {
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
        return "issue_evidence_solutions";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["issue_evidence_id, check_solution_id", "required"],
            ["issue_evidence_id, check_solution_id", "numerical", "integerOnly" => true],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "evidence" => [self::BELONGS_TO, "IssueEvidence", "issue_evidence_id"],
            "solution" => [self::BELONGS_TO, "CheckSolution", "check_solution_id"],
        ];
    }
}
