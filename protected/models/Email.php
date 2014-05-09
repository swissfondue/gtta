<?php

/**
 * This is the model class for table "emails".
 *
 * The followings are the available columns in table 'emails':
 * @property integer $id
 * @property integer $user_id
 * @property string $subject
 * @property string $content
 * @property integer $attempts
 * @property boolean $sent
 *
 * The followings are the available model relations:
 * @property User $user
 */
class Email extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Email the static model class
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
		return 'emails';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'user_id, subject, content', 'required' ),
            array( 'user_id, attempts', 'numerical', 'integerOnly' => true ),
            array( 'subject', 'length', 'max' => 1000 ),
            array( 'sent', 'boolean' ),
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
