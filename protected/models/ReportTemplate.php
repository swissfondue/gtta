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
 * @property string $reduced_intro
 * @property string $high_description
 * @property string $low_description
 * @property string $med_description
 * @property string $degree_intro
 * @property string $risk_intro
 * @property string $footer
 * @property string $none_description
 * @property string $no_vuln_description
 * @property string $info_description
 */
class ReportTemplate extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplate the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "report_templates";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name", "required"),
            array("name, header_image_path, header_image_type", "length", "max" => 1000),
            array("intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro, reduced_intro, high_description, med_description, low_description, degree_intro, risk_intro, none_description, no_vuln_description, info_description", "safe"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "l10n" => array(self::HAS_MANY, "ReportTemplateL10n", "report_template_id"),
            "summary" => array(self::HAS_MANY, "ReportTemplateSummary", "report_template_id"),
            "sections" => array(self::HAS_MANY, "ReportTemplateSection", "report_template_id"),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->name != NULL ? $this->l10n[0]->name : $this->name;
        }

        return $this->name;
    }

    /**
     * @return string localized intro.
     */
    public function getLocalizedIntro() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->intro != NULL ? $this->l10n[0]->intro : $this->intro;
        }

        return $this->intro;
    }

    /**
     * @return string localized appendix.
     */
    public function getLocalizedAppendix() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->appendix != NULL ? $this->l10n[0]->appendix : $this->appendix;
        }

        return $this->appendix;
    }

    /**
     * @return string localized vulns intro.
     */
    public function getLocalizedVulnsIntro() {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->vulns_intro != NULL ? $this->l10n[0]->vulns_intro : $this->vulns_intro;

        return $this->vulns_intro;
    }

    /**
     * @return string localized info checks intro.
     */
    public function getLocalizedInfoChecksIntro() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->info_checks_intro != NULL ? $this->l10n[0]->info_checks_intro : $this->info_checks_intro;
        }

        return $this->info_checks_intro;
    }

    /**
     * @return string localized security level intro.
     */
    public function getLocalizedSecurityLevelIntro() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->security_level_intro != NULL ? $this->l10n[0]->security_level_intro : $this->security_level_intro;
        }

        return $this->security_level_intro;
    }

    /**
     * @return string localized vuln distribution intro.
     */
    public function getLocalizedVulnDistributionIntro() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->vuln_distribution_intro != NULL ? $this->l10n[0]->vuln_distribution_intro : $this->vuln_distribution_intro;
        }

        return $this->vuln_distribution_intro;
    }

    /**
     * @return string localized reduced intro.
     */
    public function getLocalizedReducedIntro() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->reduced_intro != NULL ? $this->l10n[0]->reduced_intro : $this->reduced_intro;
        }

        return $this->reduced_intro;
    }

    /**
     * @return string localized risk intro.
     */
    public function getLocalizedRiskIntro() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->risk_intro != NULL ? $this->l10n[0]->risk_intro : $this->risk_intro;
        }

        return $this->risk_intro;
    }

    /**
     * @return string localized degree intro.
     */
    public function getLocalizedDegreeIntro() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->degree_intro != NULL ? $this->l10n[0]->degree_intro : $this->degree_intro;
        }

        return $this->degree_intro;
    }

    /**
     * @return string localized high description
     */
    public function getLocalizedHighDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->high_description != NULL ? $this->l10n[0]->high_description : $this->high_description;
        }

        return $this->high_description;
    }

    /**
     * @return string localized med description
     */
    public function getLocalizedMedDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->med_description != NULL ? $this->l10n[0]->med_description : $this->med_description;
        }

        return $this->med_description;
    }

    /**
     * @return string localized low description
     */
    public function getLocalizedLowDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->low_description != NULL ? $this->l10n[0]->low_description : $this->low_description;
        }

        return $this->low_description;
    }
    
    /**
     * @return string localized none description
     */
    public function getLocalizedNoneDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->none_description != NULL ? $this->l10n[0]->none_description : $this->none_description;
        }

        return $this->none_description;
    }
    
    /**
     * @return string localized no vuln description
     */
    public function getLocalizedNoVulnDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->no_vuln_description != NULL ? $this->l10n[0]->no_vuln_description : $this->no_vuln_description;
        }

        return $this->no_vuln_description;
    }
    
    /**
     * @return string localized info description
     */
    public function getLocalizedInfoDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->info_description != NULL ? $this->l10n[0]->info_description : $this->info_description;
        }

        return $this->info_description;
    }

    /**
     * @return string localized footer
     */
    public function getLocalizedFooter() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->footer != NULL ? $this->l10n[0]->footer : $this->footer;
        }

        return $this->footer;
    }
}
