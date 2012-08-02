<?php

/**
 * This is the model class for table "check_results_l10n".
 *
 * The followings are the available columns in table 'check_results_l10n':
 * @property integer $check_result_id
 * @property integer $language_id
 * @property string $title
 * @property string $result
 */
class CheckResultL10n extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckResultL10n the static model class
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
		return 'check_results_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'check_result_id, language_id', 'required' ),
            array( 'check_result_id, language_id', 'numerical', 'integerOnly' => true ),
            array( 'title, result', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'checkResult' => array( self::BELONGS_TO, 'CheckResult', 'check_result_id' ),
            'language'    => array( self::BELONGS_TO, 'Language',    'language_id' ),
		);
	}
}