<?php

/**
 * This is the model class for table "report_template_sections_l10n".
 *
 * The followings are the available columns in table 'report_template_sections_l10n':
 * @property integer $report_template_section_id
 * @property integer $language_id
 * @property string $intro
 * @property string $title
 */
class ReportTemplateSectionL10n extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplateSectionL10n the static model class
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
		return 'report_template_sections_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'report_template_section_id, language_id', 'required' ),
            array( 'title', 'length', 'max' => 1000 ),
            array( 'report_template_section_id, language_id', 'numerical', 'integerOnly' => true ),
            array( 'intro', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'section' => array( self::BELONGS_TO, 'ReportTemplateSection', 'report_template_section_id' ),
            'language' => array( self::BELONGS_TO, 'Language', 'language_id' ),
		);
	}
}
