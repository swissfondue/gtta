<?php

/**
 * This is the model class for table "check_fields".
 *
 * The followings are the available columns in table "check_fields":
 * @property integer $id
 * @property integer $global_check_field_id
 * @property integer $check_id
 * @property string $value
 * @property string $possible_values
 */
class CheckField extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CheckInput the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "check_fields";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("global_check_field_id, check_id", "required" ),
            array("value, possible_values", "safe"),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "l10n" => array(self::HAS_MANY, "CheckFieldL10n", "check_field_id"),
            "check" => array(self::BELONGS_TO, "Check", "check_id"),
            "systemField" => array(self::BELONGS_TO, "GlobalCheckField", "check_id"),
        );
    }

    /**
     * @return string localized name.
     */
    public function getLocalizedTitle() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->title != null ? $this->l10n[0]->title : $this->title;
        }

        return $this->title;
    }

    /**
     * @return string localized content.
     */
    public function getLocalizedContent() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->content != null ? $this->l10n[0]->content : $this->content;
        }

        return $this->content;
    }

    /**
     * Set order
     */
    public function setOrder() {
        $criteria = new CDbCriteria();
        $criteria->order = "sort_order DESC";
        $criteria->limit = 1;
        $criteria->addColumnCondition([
            "check_id" => $this->check_id,
        ]);
        $field = CheckField::model()->findByAttributes($criteria);
        $this->sort_order = $field ? $field->sort_order : 0;
    }
}
