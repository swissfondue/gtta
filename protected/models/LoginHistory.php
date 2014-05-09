<?php

/**
 * This is the model class for table "login_history".
 *
 * The followings are the available columns in table 'login_history':
 * @property integer $id
 * @property integer $user_id
 * @property string $user_name
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property User $user
 */
class LoginHistory extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LoginHistory the static model class
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
		return 'login_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'user_id, user_name', 'required' ),
            array( 'user_id', 'numerical', 'integerOnly' => true ),
            array( 'create_time', 'safe' ),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'user' => array( self::BELONGS_TO, 'User', 'user_id' ),
		);
	}
}
