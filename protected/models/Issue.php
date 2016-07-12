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
class Issue extends ActiveRecord
{
    // sql query column aliases
    public $rating;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Check the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return "issues";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ["project_id, check_id", "required"],
            ["project_id, check_id", "numerical", "integerOnly" => true],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            "project" => [self::BELONGS_TO, "Project", "project_id"],
            "check" => [self::BELONGS_TO, "Check", "check_id"],
            "evidences" => [self::HAS_MANY, "IssueEvidence", "issue_id"],
        ];
    }

    /**
     * Get issue evidence by target check
     * @param $targetCheckId
     * @return CActiveRecord
     */
    public function getEvidence($targetCheckId) {
        return IssueEvidence::model()->findByAttributes([
            "issue_id" => $this->id,
            "target_check_id" => $targetCheckId
        ]);
    }

    /**
     * Get highest rating value of this issue
     */
    public function getHighestRating() {
        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN checks AS c ON c.id = t.check_id ";
        $criteria->join .= "LEFT JOIN issue_evidences AS ie ON ie.issue_id = t.id ";
        $criteria->join .= "LEFT JOIN target_checks AS tc ON tc.id = ie.target_check_id";
        $criteria->group = "c.id";
        $criteria->select = "MAX(tc.rating) AS rating";

        return self::model()->findByPk($this->id, $criteria)->rating;
    }
}