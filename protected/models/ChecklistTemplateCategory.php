<?php

/**
 * This is the model class for table "checklist_template_categories".
 *
 * The followings are the available columns in table 'checklist_template_categories':
 * @property integer $id
 * @property string $name
 */
class ChecklistTemplateCategory extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ChecklistTemplateCategory the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "checklist_template_categories";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("name", "required"),
            array("name", "length", "max" => 1000),
        );
    }

    /**
     * @return array model relations
     */
    public function relations() {
        return array(
            "templates" => array(self::HAS_MANY, "ChecklistTemplate", "checklist_template_category_id"),
            "l10n" => array(self::HAS_MANY, "ChecklistTemplateCategoryL10n", "checklist_template_category_id"),
        );
    }

    /**
     * @return string localized name.
     */
    public function getLocalizedName() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->name != NULL ? $this->l10n[0]->name : $this->name;
        }

        return $this->name;
    }
}