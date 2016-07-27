<?php

/**
 * This is the model class for table "report_template_sections".
 *
 * The followings are the available columns in table "report_template_sections":
 * @property integer $id
 * @property string $report_template_id
 * @property integer $type
 * @property string $title
 * @property integer $content
 * @property integer $order
 */
class ReportTemplateSection extends ActiveRecord {
    /**
     * Build-in section Types
     */
    const TYPE_INTRO = 100;
    const TYPE_SECTION_SECURITY_LEVEL = 110;
    const TYPE_SECTION_VULN_DISTR = 120;
    const TYPE_SECTION_DEGREE = 130;
    const TYPE_RISK_MATRIX = 140;
    const TYPE_REDUCED_VULN_LIST = 150;
    const TYPE_VULNS= 160;
    const TYPE_INFO_CHECKS_INTRO = 170;
    const TYPE_APPENDIX = 180;
    const TYPE_FOOTER = 190;

    // build-in section titles
    public static $titles = [
        self::TYPE_INTRO => "Intro",
        self::TYPE_SECTION_SECURITY_LEVEL => "Security Level Introduction",
        self::TYPE_SECTION_VULN_DISTR => "Vuln Distribution Introduction",
        self::TYPE_SECTION_DEGREE => "Degree of Fulfillment Introduction",
        self::TYPE_RISK_MATRIX => "Risk Matrix Introduction",
        self::TYPE_REDUCED_VULN_LIST => "Reduced Vuln List Introduction",
        self::TYPE_VULNS => "Vulns Introduction",
        self::TYPE_INFO_CHECKS_INTRO => "Info Checks Introduction",
        self::TYPE_APPENDIX => "Appendix",
        self::TYPE_FOOTER => "Footer",
    ];

    // chart sections
    public static $chartTypes = [
        self::TYPE_SECTION_SECURITY_LEVEL,
        self::TYPE_SECTION_VULN_DISTR,
        self::TYPE_SECTION_DEGREE,
    ];

    /**
     * Custom section types
     */
    const TYPE_CUSTOM = 190;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ReportTemplateSection the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "report_template_sections";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["title, report_template_id, title, type", "required"],
            ["title", "length", "max" => 1000],
            ["report_template_id, order", "numerical", "integerOnly" => true],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "template" => [self::BELONGS_TO, "ReportTemplate", "report_template_id"],
        ];
    }
}
