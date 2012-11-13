<?php

/**
 * This is the model class for table "report_template_summary".
 *
 * The followings are the available columns in table 'report_template_summary':
 * @property integer $id
 * @property string $title
 * @property string $summary
 * @property float $rating_from
 * @property float $rating_to
 * @property integer $report_template_id
 */
class ReportTemplateSummary extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplateSummary the static model class
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
		return 'report_template_summary';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'title, rating_from, rating_to, report_template_id', 'required' ),
            array( 'title', 'length', 'max' => 1000 ),
            array( 'report_template_id', 'numerical', 'integerOnly' => true ),
            array( 'summary', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'     => array( self::HAS_MANY,   'ReportTemplateSummaryL10n', 'report_template_summary_id' ),
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
     * @return string localized summary.
     */
    public function getLocalizedSummary()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->summary != NULL ? $this->l10n[0]->summary : $this->summary;

        return $this->summary;
    }
}