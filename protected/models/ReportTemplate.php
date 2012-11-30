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
 * @property string $vulns_intro
 * @property string $info_checks_intro
 * @property string $security_level_intro
 * @property string $vuln_distribution_intro
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
            array( 'intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'     => array( self::HAS_MANY, 'ReportTemplateL10n',    'report_template_id' ),
            'summary'  => array( self::HAS_MANY, 'ReportTemplateSummary', 'report_template_id' ),
            'sections' => array( self::HAS_MANY, 'ReportTemplateSection', 'report_template_id' ),
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

        return $this->intro;
    }

    /**
     * @return string localized appendix.
     */
    public function getLocalizedAppendix()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->appendix != NULL ? $this->l10n[0]->appendix : $this->appendix;

        return $this->appendix;
    }

    /**
     * @return string localized vulns intro.
     */
    public function getLocalizedVulnsIntro()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->vulns_intro != NULL ? $this->l10n[0]->vulns_intro : $this->vulns_intro;

        return $this->vulns_intro;
    }

    /**
     * @return string localized info checks intro.
     */
    public function getLocalizedInfoChecksIntro()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->info_checks_intro != NULL ? $this->l10n[0]->info_checks_intro : $this->info_checks_intro;

        return $this->info_checks_intro;
    }

    /**
     * @return string localized security level intro.
     */
    public function getLocalizedSecurityLevelIntro()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->security_level_intro != NULL ? $this->l10n[0]->security_level_intro : $this->security_level_intro;

        return $this->security_level_intro;
    }

    /**
     * @return string localized vuln distribution intro.
     */
    public function getLocalizedVulnDistributionIntro()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->vuln_distribution_intro != NULL ? $this->l10n[0]->vuln_distribution_intro : $this->vuln_distribution_intro;

        return $this->vuln_distribution_intro;
    }
}