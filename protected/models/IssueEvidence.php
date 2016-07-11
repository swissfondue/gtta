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
            "fields" => [self::HAS_MANY, "IssueEvidenceField", "issue_evidence_id"],
        ];
    }

    /**
     * Set field value
     * @param $name
     * @param $value
     */
    public function setFieldValue($name, $value) {
        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN check_fields cf ON cf.id = t.check_field_id";
        $criteria->join .= " LEFT JOIN global_check_fields gcf ON gcf.id = cf.global_check_field_id";
        $criteria->addColumnCondition([
            "gcf.name" => $name,
            "t.issue_evidence_id" => $this->id,
        ]);
        $field = IssueEvidenceField::model()->find($criteria);

        if ($field) {
            $field->value = $value;
            $field->save();
        }
    }

    /**
     * Returns target check field value
     * @param $name
     * @return mixed|null
     */
    private function _getFieldValue($name) {
        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN check_fields cf ON cf.id = t.check_field_id";
        $criteria->join .= " LEFT JOIN global_check_fields gcf ON gcf.id = cf.global_check_field_id";
        $criteria->addColumnCondition([
            "gcf.name" => $name,
            "t.target_check_id" => $this->id,
        ]);
        $field = TargetCheckField::model()->find($criteria);

        if (!$field) {
            return null;
        }

        return $field->value;
    }
}