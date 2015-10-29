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
 * @property float $report_low_pedestal
 * @property float $report_med_pedestal
 * @property float $report_high_pedestal
 * @property float $report_max_rating
 * @property float $report_med_damping_low
 * @property float $report_high_damping_low
 * @property float $report_high_damping_med
 * @property string $copyright
 * @property string $logo_type
 * @property float $community_min_rating
 * @property boolean $community_allow_unverified
 * @property string $integration_key
 * @property boolean $checklist_poc
 * @property boolean $checklist_links
 * @property email $email
 * @property integer $mail_host
 * @property integer $mail_port
 * @property integer $mail_username
 * @property integer $mail_password
 * @property integer $mail_encryption
 * @property integer $git_url
 * @property integer $git_proto
 * @property integer $git_username
 * @property integer $git_password
 * @property integer $git_status
 * @property Language $language
 */
class System extends ActiveRecord {
    /**
     * Statuses
     */
    const STATUS_IDLE = 0;
    const STATUS_LICENSE_EXPIRED = 500;

    /**
     * Git statuses
     */
    const GIT_STATUS_IDLE = 0;
    const GIT_STATUS_INIT = 1;
    const GIT_STATUS_CONFIG = 2;
    const GIT_STATUS_SYNC = 3;
    const GIT_STATUS_FAILED = 4;

    /**
     * Merge strategies
     */
    const GIT_MERGE_STRATEGY_OURS = "ours";
    const GIT_MERGE_STRATEGY_THEIRS = "theirs";

    /**
     * Git repo protocols
     */
    const GIT_PROTO_HTTPS = 0;
    const GIT_PROTO_SSH = 1;

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
            self::STATUS_LICENSE_EXPIRED,
        );
    }

	/**
     * Validation rules
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("workstation_id, workstation_key, version, update_version, update_description, version_description, copyright, logo_type, integration_key", "length", "max" => 1000),
            array("status", "in", "range" => self::validStatuses()),
            array("report_low_pedestal, report_med_pedestal, report_high_pedestal, report_max_rating, report_med_damping_low, report_high_damping_low, report_high_damping_med", "numerical", "min" => 0),
            array("community_min_rating", "numerical", "min" => 0, "max" => 5),
            array("community_allow_unverified, checklist_poc, checklist_links", "boolean"),
            array("backup, timezone, update_check_time, update_time, gitUrl, git_username, git_password", "safe"),
            array("git_proto", "in", "range" => array(System::GIT_PROTO_HTTPS, System::GIT_PROTO_SSH)),
		);
	}

    /**
     * Get string status
     */
    public function getStringStatus() {
        $statuses = array(
            self::STATUS_IDLE => Yii::t("app", "The system is idle."),
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

    /**
     * Check if system is regenerating
     */
    public function getIsRegenerating() {
        $job = JobManager::buildId(RegenerateJob::ID_TEMPLATE);

        return JobManager::isRunning($job);
    }

    /**
     * Check if system is updating
     */
    public function getIsUpdating() {
        $job = JobManager::buildId(UpdateJob::ID_TEMPLATE);

        return JobManager::isRunning($job);
    }

    /**
     * Check if system is backing up
     */
    public function getIsBackingUp() {
        $job = JobManager::buildId(BackupJob::ID_TEMPLATE);

        return JobManager::isRunning($job);
    }

    /**
     * Check if system is restoring
     */
    public function getIsRestoring() {
        $job = JobManager::buildId(RestoreJob::ID_TEMPLATE);

        return JobManager::isRunning($job);
    }

    /**
     * Check if git is busy
     * @return bool
     */
    public function getGitBusy() {
        return $this->git_status != self::GIT_STATUS_IDLE && $this->git_status != self::GIT_STATUS_FAILED;
    }

    /**
     * Update git status
     * @param $status
     * @throws Exception
     */
    public function updateGitStatus($status) {
        $notIdle = array(
            self::GIT_STATUS_INIT,
            self::GIT_STATUS_CONFIG,
            self::GIT_STATUS_FAILED,
            self::GIT_STATUS_SYNC
        );

        if ($status == $this->git_status) {
            return;
        }

        if (in_array($status, $notIdle) && in_array($this->git_status, $notIdle)) {
            throw new Exception("Permission denied.");
        }

        $this->git_status = $status;
        $this->save();
    }
}
