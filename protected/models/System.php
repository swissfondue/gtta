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
 * @property integer $pid
 * @property float $report_low_pedestal
 * @property float $report_med_pedestal
 * @property float $report_high_pedestal
 * @property float $report_max_rating
 * @property float $report_med_damping_low
 * @property float $report_high_damping_low
 * @property float $report_high_damping_med
 * @property boolean $demo
 * @property string $copyright
 * @property string $logo_type
 * @property integer $demo_check_limit
 * @property float $community_min_rating
 * @property boolean $community_allow_unverified
 * @property string $integration_key
 * @property boolean $checklist_poc
 * @property boolean $checklist_links
 * @property email $email
 * @property integer $mail_max_attempts
 * @property integer $mail_host
 * @property integer $mail_port
 * @property integer $mail_username
 * @property integer $mail_password
 * @property integer $mail_crypt
 * @property Language $language
 */
class System extends ActiveRecord {
    /**
     * Statuses
     */
    const STATUS_IDLE = 0;
    const STATUS_RUNNING = 100;
    const STATUS_BACKING_UP = 200;
    const STATUS_RESTORING = 205;
    const STATUS_UPDATING = 210;
    const STATUS_PACKAGE_MANAGER = 215;
    const STATUS_REGENERATE_SANDBOX = 220;
    const STATUS_COMMUNITY_INSTALL = 230;
    const STATUS_COMMUNITY_SHARE = 240;
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
            self::STATUS_PACKAGE_MANAGER,
            self::STATUS_REGENERATE_SANDBOX,
            self::STATUS_COMMUNITY_INSTALL,
            self::STATUS_COMMUNITY_SHARE,
            self::STATUS_LICENSE_EXPIRED,
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("workstation_id, workstation_key, version, update_version, update_description, version_description, copyright, logo_type, integration_key", "length", "max" => 1000),
            array("status", "in", "range" => self::validStatuses()),
            array("report_low_pedestal, report_med_pedestal, report_high_pedestal, report_max_rating, report_med_damping_low, report_high_damping_low, report_high_damping_med, demo_check_limit", "numerical", "min" => 0),
            array("community_min_rating", "numerical", "min" => 0, "max" => 5),
            array("demo, community_allow_unverified, checklist_poc, checklist_links", "boolean"),
            array("backup, timezone, update_check_time, update_time, pid", "safe"),
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
            self::STATUS_PACKAGE_MANAGER => Yii::t("app", "The system is installing or removing packages."),
            self::STATUS_REGENERATE_SANDBOX => Yii::t("app", "The system is regenerating scripts sandbox."),
            self::STATUS_COMMUNITY_INSTALL => Yii::t("app", "The system is installing checks or packages from the community platform."),
            self::STATUS_COMMUNITY_SHARE => Yii::t("app", "The system is sharing checks or packages to the community platform."),
        );

        return $statuses[$this->status];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "language" => array(self::BELONGS_TO, "Language", "", "on" => "language.user_default IS TRUE"),
        );
    }
}
