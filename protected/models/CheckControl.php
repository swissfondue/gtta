<?php

/**
 * This is the model class for table "check_controls".
 *
 * The followings are the available columns in table 'check_controls':
 * @property integer $id
 * @property integer $check_category_id
 * @property string $name
 * @property integer $sort_order
 * @property integer $external_id
 */
class CheckControl extends ActiveRecord {
    // nearest sort order
    public $nearest_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckControl the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "check_controls";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name, check_category_id, sort_order", "required"),
            array("check_category_id, sort_order", "numerical", "integerOnly" => true),
            array("external_id", "numerical", "integerOnly" => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "l10n" => array(self::HAS_MANY, "CheckControlL10n", "check_control_id"),
            "checks" => array(self::HAS_MANY, "Check", "check_control_id"),
            "category" => array(self::BELONGS_TO, "CheckCategory", "check_category_id"),
            "checkCount" => array(self::STAT, "Check", "check_control_id"),
            "limitedCheckCount" => array(self::STAT, "Check", "check_control_id", "condition" => "NOT t.demo"),
            "customCheck" => array(self::HAS_MANY, "TargetCustomCheck", "check_control_id"),
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
