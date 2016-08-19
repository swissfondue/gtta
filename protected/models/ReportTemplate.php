<?php

/**
 * This is the model class for table "report_templates".
 *
 * The followings are the available columns in table 'report_templates':
 * @property integer $id
 * @property string $name
 * @property string $header_image_path
 * @property string $header_image_type
 * @property integer $type
 * @property string $file_path
 * @property string $footer
 * @property string $high_description
 * @property string $low_description
 * @property string $med_description
 * @property string $none_description
 * @property string $no_vuln_description
 * @property string $info_description
 * @property ReportTemplateSection[] $sections
 */
class ReportTemplate extends ActiveRecord {
    /**
     * Constants
     */
    const TYPE_RTF = 0;
    const TYPE_DOCX = 1;

    const CHART_SECTION_SECURITY_LEVEL = 100;
    const CHART_SECTION_VULN_DISTRIBUTION = 200;
    const CHART_SECTION_DEGREE_FULFILLMENT = 200;

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
     * Get valid types
     * @return array
     */
    public static function getValidTypeNames() {
        return array(
            self::TYPE_RTF => Yii::t("app", "RTF"),
            self::TYPE_DOCX => Yii::t("app", "DOCX"),
        );
    }

    /**
     * Get valid types
     * @return array
     */
    public static function getValidTypes() {
        return array(
            self::TYPE_RTF,
            self::TYPE_DOCX
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["name, type", "required"],
            ["name, header_image_path, header_image_type, file_path", "length", "max" => 1000],
            ["type", "in", "range" => self::getValidTypes()],
            ["footer, high_description, med_description, low_description, info_description, none_description, no_vuln_description", "safe"],
		];
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return [
            "l10n" => array(self::HAS_MANY, "ReportTemplateL10n", "report_template_id"),
            "summary" => array(self::HAS_MANY, "ReportTemplateSummary", "report_template_id"),
            "vulnSections" => array(self::HAS_MANY, "ReportTemplateVulnSection", "report_template_id"),
            "ratingImages" => array(self::HAS_MANY, "ReportTemplateVulnSection", "report_template_id"),
            "sections" => array(self::HAS_MANY, "ReportTemplateSection", "report_template_id"),
		];
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
     * @return string localized footer.
     */
    public function getLocalizedFooter() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->footer != NULL ? $this->l10n[0]->footer : $this->footer;
        }

        return $this->footer;
    }

    /**
     * @return string localized high description.
     */
    public function getLocalizedHighDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->high_description != NULL ? $this->l10n[0]->high_description : $this->high_description;
        }

        return $this->high_description;
    }
    
    /**
     * @return string localized med description.
     */
    public function getLocalizedMedDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->med_description != NULL ? $this->l10n[0]->med_description : $this->med_description;
        }

        return $this->med_description;
    }
    
    /**
     * @return string localized low description.
     */
    public function getLocalizedLowDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->low_description != NULL ? $this->l10n[0]->low_description : $this->low_description;
        }

        return $this->low_description;
    }
    
    /**
     * @return string localized info description.
     */
    public function getLocalizedInfoDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->info_description != NULL ? $this->l10n[0]->info_description : $this->info_description;
        }

        return $this->info_description;
    }
    
    /**
     * @return string localized none description.
     */
    public function getLocalizedNoneDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->none_description != NULL ? $this->l10n[0]->none_description : $this->none_description;
        }

        return $this->none_description;
    }
    
    /**
     * @return string localized no_vuln description.
     */
    public function getLocalizedNoVulnDescription() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->no_vuln_description != NULL ? $this->l10n[0]->no_vuln_description : $this->no_vuln_description;
        }

        return $this->no_vuln_description;
    }

    /**
     * Returns high rating image if current template
     * @return CActiveRecord
     */
    public function getRatingImage($id) {
        $id = (int) $id;

        $criteria = new CDbCriteria();
        $criteria->addCondition('report_template_id =:report_template_id');
        $criteria->addCondition('rating_id =:rating_id');
        $criteria->params = array(
            'report_template_id' => $this->id,
            'rating_id' => $id
        );

        return ReportTemplateRatingImage::model()->find($criteria);
    }

    /**
     * Check if template has section specified
     * @param int $section
     * @return bool
     */
    public function hasSection($section) {
        foreach ($this->sections as $scn) {
            if ($scn->type == $section) {
                return true;
            }
        }

        return false;
    }
}
