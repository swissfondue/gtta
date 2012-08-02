<?php

/**
 * This is the model class for table "check_solutions_l10n".
 *
 * The followings are the available columns in table 'check_solutions_l10n':
 * @property integer $check_solution_id
 * @property integer $language_id
 * @property string $title
 * @property string $solution
 */
class CheckSolutionL10n extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckSolutionL10n the static model class
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
		return 'check_solutions_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'check_solution_id, language_id', 'required' ),
            array( 'check_solution_id, language_id', 'numerical', 'integerOnly' => true ),
            array( 'title, solution', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'checkSolution' => array( self::BELONGS_TO, 'CheckSolution', 'check_solution_id' ),
            'language'      => array( self::BELONGS_TO, 'Language',      'language_id' ),
		);
	}
}