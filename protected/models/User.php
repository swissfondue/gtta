<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string $role
 * @property integer $client_id
 */
class User extends CActiveRecord
{
    /**
     * User roles.
     */
    const ROLE_ADMIN  = 'admin';
    const ROLE_USER   = 'user';
    const ROLE_CLIENT = 'client';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'email, password, role', 'required' ),
            array( 'email, password, name', 'length', 'max' => 1000 ),
            array( 'role', 'in', 'range' => array( self::ROLE_ADMIN, self::ROLE_USER, self::ROLE_CLIENT ) ),
            array( 'client_id', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'client' => array( self::BELONGS_TO, 'Client', 'client_id' ),
		);
	}

    /**
     * Check role.
     */
    static function checkRole($role)
    {
        switch ($role)
        {
            case self::ROLE_ADMIN:
            case self::ROLE_CLIENT:
                return Yii::app()->user->role == $role;

            case self::ROLE_USER:
                return in_array(Yii::app()->user->role, array( self::ROLE_ADMIN, self::ROLE_USER ));
        }

        return false;
    }
}