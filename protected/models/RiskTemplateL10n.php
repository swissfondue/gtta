<?php

/**
 * This is the model class for table "risk_templates_l10n".
 *
 * The followings are the available columns in table 'risk_templates_l10n':
 * @property integer $risk_template_id
 * @property integer $language_id
 * @property string $name
 */
class RiskTemplateL10n extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RiskTemplateL10n the static model class
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
		return 'risk_templates_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'risk_template_id, language_id', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'risk_template_id, language_id', 'numerical', 'integerOnly' => true ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'template' => array( self::BELONGS_TO, 'RiskTemplate', 'risk_template_id' ),
            'language' => array( self::BELONGS_TO, 'Language',     'language_id' ),
		);
	}
}
