<?php

/**
 * This is the model class for table "target_checklist_templates".
 *
 * The followings are the available columns in table "target_checklist_templates":
 * @property integer $target_id
 * @property integer $checklist_template_id
 * @property Target $target
 * @property ChecklistTemplate $checklistTemplate
 */
class TargetChecklistTemplate extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TargetChecklistTemplate the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "target_checklist_templates";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("target_id, checklist_template_id", "required"),
            array("target_id, checklist_template_id", "numerical", "integerOnly" => true),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "target" => array(self::BELONGS_TO, "Target", "target_id"),
            "checklistTemplate" => array(self::BELONGS_TO, "ChecklistTemplate", "checklist_template_id"),
        );
    }
}