<?php

/**
 * This is the model class for table "system".
 *
 * The followings are the available columns in table "system":
 * @property integer $id
 * @property string $backup
 * @property string $timezone
 * @property string $workstation_id
 * @property string $workstation_key
 * @property string $version
 * @property string $version_description
 * @property string $update_version
 * @property string $update_description
 * @property string $update_check_time
 * @property string $update_time
 * @property integer $status
 * @property integer $update_pid
 */
class System extends CActiveRecord {
    /**
     * Statuses
     */
    const STATUS_IDLE = 0;
    const STATUS_RUNNING = 100;
    const STATUS_BACKING_UP = 200;
    const STATUS_RESTORING = 205;
    const STATUS_UPDATING = 210;
    const STATUS_LICENSE_EXPIRED = 500;

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return System the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "system";
	}

    /**
     * Get valid statuses
     * @return array
     */
    public static function validStatuses() {
        return array(
            self::STATUS_IDLE,
            self::STATUS_RUNNING,
            self::STATUS_BACKING_UP,
            self::STATUS_RESTORING,
            self::STATUS_UPDATING,
            self::STATUS_LICENSE_EXPIRED,
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("workstation_id, workstation_key, version, update_version, update_description, version_description", "length", "max" => 1000),
            array("status", "in", "range" => self::validStatuses()),
            array("backup, timezone, update_check_time, update_time, update_pid", "safe"),
		);
	}

    /**
     * Get string status
     */
    public function getStringStatus() {
        $statuses = array(
            self::STATUS_IDLE => Yii::t("app", "The system is idle."),
            self::STATUS_RUNNING => Yii::t("app", "The system is running checks."),
            self::STATUS_BACKING_UP => Yii::t("app", "The system is backing up."),
            self::STATUS_RESTORING => Yii::t("app", "The system is restoring."),
            self::STATUS_UPDATING => Yii::t("app", "The system is updating."),
        );

        return $statuses[$this->status];
    }
}