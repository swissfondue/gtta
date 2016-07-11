<?php

/**
 * This is the model class for table "issue_evidence_fields".
 *
 * The followings are the available columns in table 'issue_evidence_fields':
 * @property integer $id
 * @property integer $issue_evidence_id
 * @property integer $check_field_id
 * @property integer $value
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
            ["issue_evidence_id, check_field_id", "required"],
            ["issue_evidence_id, check_field_id", "numerical", "integerOnly" => true],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "evidence" => [self::BELONGS_TO, "IssueEvidence", "issue_evidence_id"],
            "field" => [self::BELONGS_TO, "CheckField", "check_field_id"],
        ];
    }

    /**
     * Before save hook
     * @return bool
     */
    protected function beforeSave() {
        if ($this->field->global->name == GlobalCheckField::FIELD_TRANSPORT_PROTOCOL) {
            if (!in_array($this->value, ["TCP", "UDP"])) {
                $this->value = "TCP";
            }
        }

        return parent::beforeSave();
    }

    /**
     * Get name
     * @return mixed
     */
    public function getName() {
        return $this->field->name;
    }

    /**
     * Get type
     * @return mixed
     */
    public function getType() {
        return $this->field->type;
    }

    /**
     * Get title
     * @return mixed
     */
    public function getLocalizedTitle() {
        return $this->field->localizedTitle;
    }

    /**
     * Check if hidden by parent
     * @return mixed
     */
    public function getSuperHidden() {
        if ($this->field->superHidden) {
            return true;
        }

        return $this->field->hidden;
    }

    /**
     * Set value
     * @param $value
     * @throws Exception
     */
    public function setValue($value) {
        if ($this->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) {
            return;
        }

        if ($this->type == GlobalCheckField::TYPE_CHECKBOX) {
            $value = (bool) $value;
        }

        $this->value = $value;
        $this->save();
    }
}