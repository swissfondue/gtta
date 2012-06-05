<?php

/**
 * This is the model class for table "target_check_inputs".
 *
 * The followings are the available columns in table 'target_check_inputs':
 * @property integer $target_id
 * @property integer $check_input_id
 * @property integer $check_id
 * @property string $value
 * @property string $file
 */
class TargetCheckInput extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheckInput the static model class
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
		return 'target_check_inputs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'target_id, check_input_id, check_id', 'required' ),
            array( 'target_id, check_input_id, check_id', 'numerical', 'integerOnly' => true ),
            array( 'file', 'length', 'max' => 1000 ),
            array( 'value', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'target' => array( self::BELONGS_TO, 'Target',     'target_id' ),
            'input'  => array( self::BELONGS_TO, 'CheckInput', 'check_input_id' ),
            'check'  => array( self::BELONGS_TO, 'Check',      'check_id' ),
		);
	}
}