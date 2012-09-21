<?php

/**
 * This is the model class for table "projects".
 *
 * The followings are the available columns in table 'projects':
 * @property integer $id
 * @property integer $client_id
 * @property string $year
 * @property string $name
 * @property string $deadline
 * @property string $status
 */
class Project extends CActiveRecord
{
    /**
     * Project statuses.
     */
    const STATUS_OPEN        = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINISHED    = 'finished';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
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
		return 'projects';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name, year', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'year', 'length', 'max' => 4 ),
            array( 'status', 'in', 'range' => array( self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_FINISHED ) ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'client'  => array( self::BELONGS_TO, 'Client',        'client_id' ),
            'details' => array( self::HAS_MANY,   'ProjectDetail', 'project_id' ),
            'users'   => array( self::MANY_MANY,  'User',          'project_users(project_id, user_id)' ),
		);
	}
}