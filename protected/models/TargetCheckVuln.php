<?php

/**
 * This is the model class for table "target_check_vulns".
 *
 * The followings are the available columns in table 'target_check_vulns':
 * @property integer $target_id
 * @property integer $check_id
 * @property integer $user_id
 * @property string $deadline
 * @property string $status
 */
class TargetCheckVuln extends CActiveRecord
{
    /**
     * Vulnerability statuses.
     */
    const STATUS_OPEN     = 'open';
    const STATUS_RESOLVED = 'resolved';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheckVuln the static model class
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
		return 'target_check_vulns';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'target_id, check_id', 'required' ),
            array( 'target_id, check_id, user_id', 'numerical', 'integerOnly' => true ),
            array( 'status', 'in', 'range' => array( self::STATUS_OPEN, self::STATUS_RESOLVED ) ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'target'      => array( self::BELONGS_TO, 'Target',      'target_id' ),
            'check'       => array( self::BELONGS_TO, 'Check',       'check_id' ),
            'user'        => array( self::BELONGS_TO, 'User',        'user_id' ),
            'targetCheck' => array( self::BELONGS_TO, 'TargetCheck', array( 'target_id', 'check_id' ) ),
		);
	}

    /**
     * Check if vulnerability is overdued.
     */
    public function getOverdued()
    {
        if (!$this->deadline)
            return false;

        if ($this->status == self::STATUS_RESOLVED)
            return false;

        $deadline = new DateTime($this->deadline . ' 00:00:00');
        $today    = new DateTime();
        $today->setTime(0, 0, 0);

        if ($today > $deadline)
            return true;

        return false;
    }
}