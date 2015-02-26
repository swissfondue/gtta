<?php
/**
 * This is the model class for table "checklist_templates".
 *
 * The followings are the available columns in table 'checklist_templates':
 * @property integer $id
 * @property integer $checklist_template_category_id
 * @property string $name
 * @property string $description
 */
class ChecklistTemplate extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ChecklistTemplate the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "checklist_templates";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("name, checklist_template_category_id", "required"),
            array("checklist_template_category_id", "numerical", "integerOnly" => true),
            array("name", "length", "max" => 1000),
        );
    }

    /**
     * @return array model relations
     */
    public function relations() {
        return array(
            "category"        => array(self::BELONGS_TO, "ChecklistTemplateCategory", "checklist_template_category_id"),
            "checks"          => array(self::HAS_MANY, "ChecklistTemplateCheck", "checklist_template_id"),
            "l10n"            => array(self::HAS_MANY, "ChecklistTemplateL10n", "checklist_template_id"),
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

    /**
     * Get number of template checks
     * @return string
     */
    public function getCheckCount() {
        return count($this->checks);
    }

    /**
     * Returns check categories of current checklist template
     * @return array
     */
    public function getCheckCategories() {
        $categoryIds = array();

        $checks = ChecklistTemplateCheck::model()->findAllByAttributes(array(
            "checklist_template_id" => $this->id
        ));

        foreach ($checks as $check) {
            $categoryIds[] = $check->check->control->category->id;
        }

        $categoryIds = array_unique($categoryIds);

        return CheckCategory::model()->findAllByAttributes(array(
            "id" => $categoryIds
        ));
    }
}