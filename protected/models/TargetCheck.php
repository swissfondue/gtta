<?php

/**
 * This is the model class for table "target_checks".
 *
 * The followings are the available columns in table 'target_checks':
 * @property integer $target_id
 * @property integer $check_id
 * @property string $result
 * @property integer $rating
 * @property string $status
 * @property string $target_file
 * @property string $percentage_file
 * @property float $percent
 */
class TargetCheck extends CActiveRecord
{
    /**
     * Check statuses.
     */
    const STATUS_OPEN        = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINISHED    = 'finished';

    /**
     * Result ratings.
     */
    const RATING_HIDDEN    = 0;
    const RATING_INFO      = 10;
    const RATING_LOW_RISK  = 20;
    const RATING_MED_RISK  = 30;
    const RATING_HIGH_RISK = 40;

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
            array( 'target_id, check_id, rating', 'numerical', 'integerOnly' => true ),
            array( 'target_file, percentage_file', 'length', 'max' => 1000 ),
            array( 'percent', 'numerical', 'max' => 100.0 ),
            array( 'status', 'in', 'range' => array( self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_FINISHED ) ),
            array( 'rating', 'in', 'range' => array( self::RATING_HIDDEN, self::RATING_INFO, self::RATING_LOW_RISK, self::RATING_MED_RISK, self::RATING_HIGH_RISK ) ),
            array( 'result', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'target' => array( self::BELONGS_TO, 'Target', 'target_id' ),
            'check'  => array( self::BELONGS_TO, 'Check',  'check_id' ),
		);
	}
}