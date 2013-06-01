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
 */
class ProjectGtCheck extends CActiveRecord
{
    /**
     * Check statuses.
     */
    const STATUS_OPEN        = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_STOP        = 'stop';
    const STATUS_FINISHED    = 'finished';

    /**
     * Result ratings.
     */
    const RATING_NONE      = 'none';
    const RATING_HIDDEN    = 'hidden';
    const RATING_INFO      = 'info';
    const RATING_LOW_RISK  = 'low_risk';
    const RATING_MED_RISK  = 'med_risk';
    const RATING_HIGH_RISK = 'high_risk';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtCheck the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_gt_checks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('project_id, gt_check_id, user_id, language_id', 'required'),
            array('project_id, gt_check_id, pid, port, language_id, user_id', 'numerical', 'integerOnly' => true),
            array('target_file, result_file, protocol, target', 'length', 'max' => 1000),
            array('status', 'in', 'range' => array(self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_STOP, self::STATUS_FINISHED)),
            array('rating', 'in', 'range' => array(self::RATING_HIDDEN, self::RATING_INFO, self::RATING_LOW_RISK, self::RATING_MED_RISK, self::RATING_HIGH_RISK)),
            array('result, started, table_result', 'safe'),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'check' => array(self::BELONGS_TO, 'GtCheck', 'gt_check_id'),
            'language' => array(self::BELONGS_TO, 'Language', 'language_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

    /**
     * Set automation error.
     */
    public function automationError($error)
    {
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