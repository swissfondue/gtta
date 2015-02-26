<?php

/**
 * This is the model class for table "checklist_template_category_l10n".
 *
 * The followings are the available columns in table 'checklist_template_category_l10n':
 * @property integer $checklist_template_category_id
 * @property string $name
 */
class ChecklistTemplateCategoryL10n extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ChecklistTemplateCategoryL10n the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "checklist_template_categories_l10n";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("checklist_template_category_id, language_id", "required"),
            array("checklist_template_category_id, language_id", "numerical", "integerOnly" => true),
            array("name", "length", "max" => 1000),
        );
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            "category" => array(self::BELONGS_TO, "ChecklistTemplateCategory", "checklist_template_category_id"),
            "language" => array(self::BELONGS_TO, "Language", "language_id"),
        );
    }
}