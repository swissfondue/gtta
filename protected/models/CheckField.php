<?php

/**
 * This is the model class for table "check_fields".
 *
 * The followings are the available columns in table "check_fields":
 * @property integer $id
 * @property integer $global_check_field_id
 * @property integer $check_id
 * @property string $value
 * @property GlobalCheckField $global
 * @property CheckFieldL10n $l10n
 * @property Check $check
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

    /**
     * Get type
     * @return mixed
     */
    public function getType() {
        return $this->global->type;
    }

    /**
     * Get name
     * @return string
     */
    public function getName() {
        return $this->global->name;
    }

    /**
     * Return value if it exist in any language (for checkbox or radio)
     * @param null $languageId
     * @return mixed|null|string
     */
    public function getValue($languageId = null) {
        if ($languageId) {
            $l10n = CheckFieldL10n::model()->findByAttributes([
                "check_field_id" => $this->id,
                "language_id" => $languageId
            ]);

            return $l10n->value ? $l10n->value : null;
        }

        $value = $this->value;

        // use user_default language
        if (!$value) {
            $language = Language::model()->findByAttributes(["user_default" => true]);

            $l10n = CheckFieldL10n::model()->findByAttributes([
                "check_field_id" => $this->id,
                "language_id" => $language->id
            ]);
            $value = $l10n->value ? $l10n->value : null;
        }

        if (!$value) {
            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(["check_field_id" => $this->id]);
            $criteria->addNotInCondition("language_id", [$language->id]);
            $criteria->addCondition("value IS NOT NULL");
            $l10n = CheckFieldL10n::model()->find($criteria);

            if (!$l10n) {
                return null;
            }
        }

        return $value;
    }

    /**
     * Set value
     * @param $value
     * @param null $languageId
     */
    public function setValue($value, $languageId = null) {
        if (!$languageId) {
            $this->value = $value;
            $this->save();

            return;
        }

        $l10n = CheckFieldL10n::model()->findByAttributes([
            "check_field_id" => $this->id,
            "language_id" => $languageId
        ]);

        $l10n->setValue($value);
    }

    /**
     * Get title
     * @return mixed
     */
    public function getLocalizedTitle() {
        return $this->global->localizedTitle;
    }

    /**
     * Check if hidden
     * @return bool
     */
    public function getHidden() {
        return $this->global->hidden;
    }
}
