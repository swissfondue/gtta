<?php

/**
 * This is the model class for table "target_custom_checks".
 *
 * The followings are the available columns in table "target_custom_checks":
 * @property integer $id
 * @property integer $target_id
 * @property integer $check_control_id
 * @property integer $user_id
 * @property string $name
 * @property string $background_info
 * @property string $question
 * @property string $result
 * @property string $solution_title
 * @property string $solution
 * @property integer $reference
 * @property integer $rating
 * @property string $poc
 * @property string $links
 * @property Target $target
 * @property CheckControl $control
 * @property User $user
 * @property TargetCustomCheckAttachment[] $attachments
 */
class TargetCustomCheck extends ActiveRecord {
    /**
     * Result ratings.
     */
    const RATING_NONE = 0;
    const RATING_NO_VULNERABILITY = 10;
    const RATING_HIDDEN = 20;
    const RATING_INFO = 50;
    const RATING_LOW_RISK = 100;
    const RATING_MED_RISK = 200;
    const RATING_HIGH_RISK = 500;

    /**
     * @var integer max reference.
     */
    public $max_reference;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheck the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "target_custom_checks";
	}

    /**
     * Get valid rating values
     * @return array
     */
    public static function getValidRatings() {
        return array(
            self::RATING_NONE,
            self::RATING_NO_VULNERABILITY,
            self::RATING_HIDDEN,
            self::RATING_INFO,
            self::RATING_LOW_RISK,
            self::RATING_MED_RISK,
            self::RATING_HIGH_RISK,
        );
    }

    /**
     * Get rating names
     * @return array
     */
    public static function getRatingNames() {
        return array(
            self::RATING_NONE => Yii::t("app", "No Test Done"),
            self::RATING_NO_VULNERABILITY => Yii::t("app", "No Vulnerability"),
            self::RATING_HIDDEN => Yii::t("app", "Hidden"),
            self::RATING_INFO =>  Yii::t("app", "Info"),
            self::RATING_LOW_RISK => Yii::t("app", "Low Risk"),
            self::RATING_MED_RISK => Yii::t("app", "Med Risk"),
            self::RATING_HIGH_RISK => Yii::t("app", "High Risk"),
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("target_id, check_control_id, user_id, reference, rating", "required"),
            array("target_id, check_control_id, user_id", "numerical", "integerOnly" => true),
            array("name, solution_title", "length", "max" => 1000),
            array("rating", "in", "range" => self::getValidRatings()),
            array("solution, result, question, background_info, poc, links", "safe"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "target" => array(self::BELONGS_TO, "Target", "target_id"),
            "control" => array(self::BELONGS_TO, "CheckControl", "check_control_id"),
            "user" => array(self::BELONGS_TO, "User", "user_id"),
            "attachments" => array(self::HAS_MANY, "TargetCustomCheckAttachment", "target_custom_check_id"),
		);
	}
}
