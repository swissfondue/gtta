<?php

/**
 * This is the model class for table "global_check_fields".
 *
 * The followings are the available columns in table "global_check_fields":
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $title
 * @property boolean $hidden
 */
class GlobalCheckField extends ActiveRecord {
    /**
     * Field types.
     */
    const TYPE_TEXT = 10;
    const TYPE_TEXTAREA = 20;
    const TYPE_WYSIWYG = 30;
    const TYPE_WYSIWYG_READONLY = 31;
    const TYPE_RADIO = 40;
    const TYPE_CHECKBOX = 50;

    public static $fieldTypes = [
        self::TYPE_TEXT => "Text",
        self::TYPE_TEXTAREA => "Textarea",
        self::TYPE_WYSIWYG => "WYSIWYG",
        self::TYPE_WYSIWYG_READONLY => "WYSIWYG (Read Only)",
        self::TYPE_RADIO => "Radio",
        self::TYPE_CHECKBOX => "Checkbox",
    ];

    // readonly fields
    const FIELD_BACKGROUND_INFO = "background_info";
    const FIELD_QUESTION = "question";
    const FIELD_HINTS = "hints";
    const FIELD_RESULT = "result";
    const FIELD_POC = "poc";

    public static $system = [
        self::FIELD_BACKGROUND_INFO,
        self::FIELD_QUESTION,
        self::FIELD_HINTS,
        self::FIELD_RESULT,
        self::FIELD_POC,
    ];

    public $nearest_sort_order;

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
        return "global_check_fields";
    }

    /**
     * Before save hook
     * @return bool
     */
    protected function beforeSave() {
        $field = self::model()->findByAttributes([
            "name" => $this->name
        ]);

        if ($field) {
            if ($this->id != $field->id) {
                $this->addError("name", Yii::t("app", "Field with that name already exists."));

                return false;
            }
        }

        if ($this->id) {
            $field = self::model()->findByPk($this->id);

            if (in_array($field->name, GlobalCheckField::$system) && $this->name != $field->name) {
                $this->addError("name", Yii::t("app", "Access denied."));

                return false;
            }

            if ($this->type != $field->type) {
                $this->addError("type", Yii::t("app", "You cannot change type of existing field."));

                return false;
            }
        }

        return parent::beforeSave();
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("type, name, title", "required" ),
            array("name, title", "length", "max" => 1000 ),
            array("type", "in", "range" => [
                self::TYPE_TEXT,
                self::TYPE_TEXTAREA,
                self::TYPE_WYSIWYG,
                self::TYPE_WYSIWYG_READONLY,
                self::TYPE_RADIO,
                self::TYPE_CHECKBOX,
            ]),
            array("hidden", "boolean"),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "l10n" => array(self::HAS_MANY, "GlobalCheckFieldL10n", "global_check_field_id"),
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
}
