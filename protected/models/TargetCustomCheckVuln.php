<?php

/**
 * This is the model class for table "target_custom_check_vulns".
 *
 * The followings are the available columns in table "target_custom_check_vulns":
 * @property integer $target_custom_check_id
 * @property integer $user_id
 * @property string $deadline
 * @property string $status
 */
class TargetCustomCheckVuln extends ActiveRecord {
    /**
     * Vulnerability statuses.
     */
    const STATUS_OPEN = 0;
    const STATUS_RESOLVED = 100;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TargetCustomCheckVuln the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "target_custom_check_vulns";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("target_custom_check_id", "required"),
            array("target_custom_check_id, user_id", "numerical", "integerOnly" => true),
            array("status", "in", "range" => array(self::STATUS_OPEN, self::STATUS_RESOLVED)),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "user" => array(self::BELONGS_TO, "User", "user_id"),
            "targetCustomCheck" => array(self::BELONGS_TO, "TargetCustomCheck", "target_custom_check_id"),
        );
    }

    /**
     * Check if vulnerability is overdued.
     */
    public function getOverdued() {
        if (!$this->deadline) {
            return false;
        }

        if ($this->status == self::STATUS_RESOLVED) {
            return false;
        }

        $deadline = new DateTime($this->deadline . " 00:00:00");
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        if ($today > $deadline) {
            return true;
        }

        return false;
    }
}