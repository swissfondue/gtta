<?php

/**
 * This is the model class for table "report_template_sections".
 *
 * The followings are the available columns in table 'report_template_sections':
 * @property integer $id
 * @property string $title
 * @property string $intro
 * @property integer $report_template_id
 * @property integer $check_category_id
 * @property integer $max_sort_order
 */
class ReportTemplateSection extends ActiveRecord
{
    /**
     * @var integer max sort order.
     */
    public $max_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplateSection the static model class
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
		return 'report_template_sections';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'title, check_category_id, report_template_id', 'required' ),
            array( 'title', 'length', 'max' => 1000 ),
            array( 'check_category_id, report_template_id', 'numerical', 'integerOnly' => true ),
            array( 'intro', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'     => array( self::HAS_MANY,   'ReportTemplateSectionL10n', 'report_template_section_id' ),
            'template' => array( self::BELONGS_TO, 'ReportTemplate', 'report_template_id' ),
		);
	}

    /**
     * @return string localized title.
     */
    public function getLocalizedTitle()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->title != NULL ? $this->l10n[0]->title : $this->title;

        return $this->title;
    }

    /**
     * @return string localized intro.
     */
    public function getLocalizedIntro()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->intro != NULL ? $this->l10n[0]->intro : $this->intro;

        return $this->intro;
    }
}
