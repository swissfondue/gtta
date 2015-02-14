<?php

/**
 * This is the model class for table "checklist_template_checks".
 *
 * The followings are the available columns in table 'checklist_template_checks':
 * @property integer checklist_template_id
 * @property integer check_id
 */
class ChecklistTemplateCheck extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Check the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "checklist_template_checks";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("checklist_template_id, check_id", "required"),
            array("checklist_template_id, check_id", "numerical", "integerOnly" => true),
        );
    }

    /**
     * @return array model relations
     */
    public function relations() {
        return array(
            "template" => array(self::BELONGS_TO, "ChecklistTemplate", "checklist_template_id"),
            "check"    => array(self::BELONGS_TO, "Check", "check_id"),
        );
    }
}