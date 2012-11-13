<?php

/**
 * This is the model class for table "report_templates".
 *
 * The followings are the available columns in table 'report_templates':
 * @property integer $id
 * @property string $name
 * @property string $header_image_path
 * @property string $header_image_type
 * @property string $intro
 * @property string $appendix
 */
class ReportTemplate extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplate the static model class
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
		return 'report_templates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name', 'required' ),
            array( 'name, header_image_path, header_image_type', 'length', 'max' => 1000 ),
            array( 'intro, appendix', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'    => array( self::HAS_MANY, 'ReportTemplateL10n',    'report_template_id' ),
            'summary' => array( self::HAS_MANY, 'ReportTemplateSummary', 'report_template_id' ),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->name != NULL ? $this->l10n[0]->name : $this->name;

        return $this->name;
    }

    /**
     * @return string localized intro.
     */
    public function getLocalizedIntro()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->intro != NULL ? $this->l10n[0]->intro : $this->intro;

        return $this->name;
    }

    /**
     * @return string localized appendix.
     */
    public function getLocalizedAppendix()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->appendix != NULL ? $this->l10n[0]->appendix : $this->appendix;

        return $this->name;
    }
}