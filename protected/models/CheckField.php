<?php

/**
 * This is the model class for table "check_fields".
 *
 * The followings are the available columns in table "check_fields":
 * @property integer $id
 * @property integer $check_id
 * @property integer $type
 * @property boolean $project_only
 * @property string $name
 * @property integer $order
 * @property boolean $hidden
 */
class CheckField extends ActiveRecord {
    /**
     * Field types.
     */
    const TYPE_TEXT = 10;
    const TYPE_TEXTAREA = 20;
    const TYPE_WYSIWYG = 30;
    const TYPE_WYSIWYG_READONLY = 31;
    const TYPE_RADIO = 40;
    const TYPE_CHECKBOX = 50;

    // system fields
    const FIELD_BACKGROUND_INFO = "background_info";
    const FIELD_QUESTION = "question";
    const FIELD_HINTS = "hints";
    const FIELD_RESULT = "result";
    public $system = [
        "background_info",
        "question",
        "hints",
        "result",
    ];

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
        return "check_fields";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("check_id, type, name, sort_order", "required" ),
            array("name", "length", "max" => 1000 ),
            array("sort_order", "numerical", "integerOnly" => true, "min" => 0 ),
            array("type", "in", "range" => [
                self::TYPE_TEXT,
                self::TYPE_TEXTAREA,
                self::TYPE_WYSIWYG,
                self::TYPE_WYSIWYG_READONLY,
                self::TYPE_RADIO,
                self::TYPE_CHECKBOX,
            ]),
            array("project_only", "boolean"),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "l10n" => array(self::HAS_MANY, "CheckFieldL10n", "check_field_id"),
            "check" => array(self::BELONGS_TO, "Check", "check_id"),
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
