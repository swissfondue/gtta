<?php

/**
 * This is the model class for table "report_templates_l10n".
 *
 * The followings are the available columns in table 'report_templates_l10n':
 * @property integer $report_template_id
 * @property integer $language_id
 * @property string $name
 * @property string $intro
 * @property string $appendix
 * @property string $separate_vulns_intro
 * @property string $vulns_intro
 * @property string $info_checks_intro
 */
class ReportTemplateL10n extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplateL10n the static model class
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
		return 'report_templates_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'report_template_id, language_id', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'report_template_id, language_id', 'numerical', 'integerOnly' => true ),
            array( 'intro, appendix, separate_vulns_intro, vulns_intro, info_checks_intro', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'template' => array( self::BELONGS_TO, 'ReportTemplate', 'report_template_id' ),
            'language' => array( self::BELONGS_TO, 'Language',       'language_id' ),
		);
	}
}