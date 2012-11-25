<?php

/**
 * This is the model class for table "target_checks".
 *
 * The followings are the available columns in table 'target_checks':
 * @property integer $target_id
 * @property integer $check_id
 * @property string $result
 * @property string $rating
 * @property string $status
 * @property string $target_file
 * @property string $result_file
 * @property string $started
 * @property integer $pid
 * @property integer $language_id
 * @property string $protocol
 * @property integer $port
 * @property string $override_target
 * @property integer $user_id
 */
class TargetCheck extends CActiveRecord
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
     * Export columns.
     */
    const COLUMN_TARGET          = 'target';
    const COLUMN_NAME            = 'name';
    const COLUMN_REFERENCE       = 'reference';
    const COLUMN_BACKGROUND_INFO = 'background';
    const COLUMN_QUESTION        = 'question';
    const COLUMN_RESULT          = 'result';
    const COLUMN_SOLUTION        = 'solution';
    const COLUMN_RATING          = 'rating';
    const COLUMN_ASSIGNED_USER   = 'assigned_user';
    const COLUMN_DEADLINE        = 'deadline';
    const COLUMN_STATUS          = 'status';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheck the static model class
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
		return 'target_checks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'target_id, check_id', 'required' ),
            array( 'target_id, check_id, pid, port, language_id, user_id', 'numerical', 'integerOnly' => true ),
            array( 'target_file, result_file, protocol, override_target', 'length', 'max' => 1000 ),
            array( 'status', 'in', 'range' => array( self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_STOP, self::STATUS_FINISHED ) ),
            array( 'rating', 'in', 'range' => array( self::RATING_HIDDEN, self::RATING_INFO, self::RATING_LOW_RISK, self::RATING_MED_RISK, self::RATING_HIGH_RISK ) ),
            array( 'result, started', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'target'   => array( self::BELONGS_TO, 'Target',          'target_id'   ),
            'check'    => array( self::BELONGS_TO, 'Check',           'check_id'    ),
            'language' => array( self::BELONGS_TO, 'Language',        'language_id' ),
            'user'     => array( self::BELONGS_TO, 'User',            'user_id'     ),
            'vuln'     => array( self::HAS_ONE,    'TargetCheckVuln', array( 'target_id', 'check_id' ) ),
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

        $this->result = $message;
        $this->status = TargetCheck::STATUS_FINISHED;
        $this->save();
    }
}