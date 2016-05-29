<?php

/**
 * This is the model class for table "check_fields".
 *
 * The followings are the available columns in table "check_fields":
 * @property integer $id
 * @property integer $global_check_field_id
 * @property integer $check_id
 * @property string $value
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
            array("value", "safe"),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "l10n" => array(self::HAS_MANY, "CheckFieldL10n", "check_field_id"),
            "check" => array(self::BELONGS_TO, "Check", "check_id"),
            "global" => array(self::BELONGS_TO, "GlobalCheckField", "global_check_field_id"),
        );
    }
}
