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
class ReportTemplateL10n extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplateL10n the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "report_templates_l10n";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("report_template_id, language_id", "required"),
            array("name", "length", "max" => 1000),
            array("report_template_id, language_id", "numerical", "integerOnly" => true),
            array("intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro, reduced_intro, high_description, med_description, low_description, degree_intro, risk_intro, none_description, no_vuln_description, info_description", "safe"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "template" => array(self::BELONGS_TO, "ReportTemplate", "report_template_id"),
            "language" => array(self::BELONGS_TO, "Language", "language_id"),
		);
	}
}
