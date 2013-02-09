<?php

/**
 * This is the model class for table "check_inputs_l10n".
 *
 * The followings are the available columns in table 'check_inputs_l10n':
 * @property integer $check_input_id
 * @property integer $language_id
 * @property string $name
 * @property string $description
 */
class CheckInputL10n extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckInputL10n the static model class
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
		return 'check_inputs_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'check_input_id, language_id', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'check_input_id, language_id', 'numerical', 'integerOnly' => true ),
            array( 'description', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'checkInput' => array( self::BELONGS_TO, 'CheckInput', 'check_input_id' ),
            'language'   => array( self::BELONGS_TO, 'Language',   'language_id' ),
		);
	}
}