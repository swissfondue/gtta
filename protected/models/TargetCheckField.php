<?php

/**
 * This is the model class for table "target_check_field".
 *
 * The followings are the available columns in table "target_check_fields":
 * @property integer $id
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
            array("target_check_id, check_field_id", "required" ),
            array("target_check_id, check_field_id", "numerical", "integerOnly" => true),
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

    /**
     * Reset field value
     */
    public function reset() {
        if ($this->type != GlobalCheckField::TYPE_WYSIWYG_READONLY) {
            $this->value = "";
            $this->save();
        }
    }
}
