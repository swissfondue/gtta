<?php

/**
 * This is the model class for table "target_check_field".
 *
 * The followings are the available columns in table "target_check_fields":
 * @property integer $target_check_id
 * @property integer $check_field_id
 * @property string $value
 * @property boolean $hidden
 */
class TargetCheckField extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CheckInput the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * Set order field
     * @return bool
     */
    protected function beforeSave() {
        $this->setOrder();

        return parent::beforeSave();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "target_check_fields";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("target_check_id, check_field_id, sort_order", "required" ),
            array("target_check_id, check_field_id, sort_order", "numerical", "integerOnly" => true),
            array("hidden", "boolean"),
            array("value", "safe"),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "targetCheck" => array(self::BELONGS_TO, "TargetCheck", "target_check_id"),
            "field" => array(self::BELONGS_TO, "CheckField", "check_field_id"),
        );
    }

    public function setOrder() {}
}
