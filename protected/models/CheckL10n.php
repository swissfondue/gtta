<?php

/**
 * This is the model class for table "checks_l10n".
 *
 * The followings are the available columns in table 'checks_l10n':
 * @property integer $check_id
 * @property integer $language_id
 * @property string $name
 * @property string $background_info
 * @property string $impact_info
 * @property string $manual_info
 */
class CheckL10n extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckL10n the static model class
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
		return 'checks_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'check_id, language_id', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'check_id, language_id', 'numerical', 'integerOnly' => true ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'check'    => array( self::BELONGS_TO, 'Check',    'check_id' ),
            'language' => array( self::BELONGS_TO, 'Language', 'language_id' ),
		);
	}
}