<?php

/**
 * This is the model class for table "project_gt_checks".
 *
 * The followings are the available columns in table 'project_gt_checks':
 * @property integer $project_id
 * @property integer $gt_check_id
 * @property integer $user_id
 * @property integer $language_id
 * @property string $target
 * @property integer $port
 * @property string $protocol
 * @property string $target_file
 * @property string $result_file
 * @property string $result
 * @property string $table_result
 * @property string $started
 * @property integer $pid
 * @property string $rating
 * @property string $status
 * @property string $solution
 * @property string $solution_title
 * @property User $user
 */
class ProjectGtCheck extends ActiveRecord {
    /**
     * Check statuses.
     */
    const STATUS_OPEN = "open";
    const STATUS_IN_PROGRESS = "in_progress";
    const STATUS_STOP = "stop";
    const STATUS_FINISHED = "finished";

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
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtCheck the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'project_gt_checks';
	}

    /**
     * Get valid rating values
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
            array('project_id, gt_check_id, user_id, language_id', 'required'),
            array('project_id, gt_check_id, pid, port, language_id, user_id', 'numerical', 'integerOnly' => true),
            array('target_file, result_file, protocol, target', 'length', 'max' => 1000),
            array('status', 'in', 'range' => array(self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_STOP, self::STATUS_FINISHED)),
            array('rating', 'in', 'range' => self::getValidRatings()),
            array('result, started, table_result, solution, solution_title', 'safe'),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'check' => array(self::BELONGS_TO, 'GtCheck', 'gt_check_id'),
            'language' => array(self::BELONGS_TO, 'Language', 'language_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'vuln' => array(self::HAS_ONE, 'ProjectGtCheckVuln', array('project_id', 'gt_check_id')),
            'solutions' => array(self::HAS_MANY, 'ProjectGtCheckSolution', array('project_id', 'gt_check_id')),
            'attachments' => array(self::HAS_MANY, 'ProjectGtCheckAttachment', array('project_id', 'gt_check_id')),
		);
	}

    /**
     * Set automation error.
     */
    public function automationError($error) {
        $uniqueHash = strtoupper(substr(hash('sha256', time() . rand() . $error), 0, 16));

        Yii::log($uniqueHash . ' ' . $error, 'error');
        Yii::getLogger()->flush(true);

        $message = Yii::t('app', 'Internal server error. Please send this error code to the administrator - {code}.', array(
            '{code}' => $uniqueHash
        ));

        if (!$this->result) {
            $this->result = '';
        } else {
            $this->result .= "\n";
        }

        $this->result .= $message;
        $this->status = self::STATUS_FINISHED;
        $this->save();
    }
}
