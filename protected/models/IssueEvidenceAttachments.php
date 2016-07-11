<?php

/**
 * This is the model class for table "issue_evidence_attachments".
 *
 * The followings are the available columns in table 'issue_evidence_attachments':
 * @property integer $issue_evidence_id
 * @property string $name
 * @property string $type
 * @property string $path
 * @property integer $size
 * @property TargetCheck $targetCheck
 */
class IssueEvidenceAttachment extends ActiveRecord implements IVariableScopeObject {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return IssueEvidenceAttachment the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "issue_evidence_attachments";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("issue_evidence_id, name, type, path, size", "required"),
            array("issue_evidence_id, size", "numerical", "integerOnly" => true),
            array("name, type, path", "length", "max" => 1000),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "evidence" => array(self::BELONGS_TO, "IssueEvidence", "issue_evidence_id"),
        );
    }
}
