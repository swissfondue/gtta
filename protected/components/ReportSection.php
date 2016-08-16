<?php

/**
 * Report section class
 */
class ReportSection {
    /**
     * Built-in section types
     */
    const TYPE_INTRO = 10;
    const TYPE_SUMMARY = 11;
    const TYPE_CHART_SECURITY_LEVEL = 20;
    const TYPE_CHART_VULNERABILITY_DISTRIBUTION = 30;
    const TYPE_CHART_DEGREE_OF_FULFILLMENT = 40;
    const TYPE_RISK_MATRIX = 50;
    const TYPE_REDUCED_VULNERABILITY_LIST = 60;
    const TYPE_VULNERABILITIES = 70;
    const TYPE_INFO_CHECKS = 80;
    const TYPE_APPENDIX = 90;
    const TYPE_ATTACHMENTS = 100;
    const TYPE_CUSTOM = 200;

    /**
     * Get type titles
     * @return array
     */
    public static function getTypeTitles() {
        return [
            self::TYPE_INTRO => Yii::t("app", "Introduction"),
            self::TYPE_SUMMARY => Yii::t("app", "Summary"),
            self::TYPE_RISK_MATRIX => Yii::t("app", "Risk Matrix"),
            self::TYPE_REDUCED_VULNERABILITY_LIST => Yii::t("app", "Reduced Vulnerability List"),
            self::TYPE_VULNERABILITIES => Yii::t("app", "Vulnerability List"),
            self::TYPE_INFO_CHECKS => Yii::t("app", "Info Checks"),
            self::TYPE_APPENDIX => Yii::t("app", "Appendix"),
            self::TYPE_ATTACHMENTS => Yii::t("app", "Attachments"),
            self::TYPE_CHART_SECURITY_LEVEL => Yii::t("app", "Security Level"),
            self::TYPE_CHART_VULNERABILITY_DISTRIBUTION => Yii::t("app", "Vulnerability Distribution"),
            self::TYPE_CHART_DEGREE_OF_FULFILLMENT => Yii::t("app", "Degree of Fulfillment"),
            self::TYPE_CUSTOM => Yii::t("app", "Custom"),
        ];
    }

    /**
     * Get chart types
     * @return array
     */
    public static function getChartTypes() {
        return [
            self::TYPE_CHART_SECURITY_LEVEL,
            self::TYPE_CHART_VULNERABILITY_DISTRIBUTION,
            self::TYPE_CHART_DEGREE_OF_FULFILLMENT,
        ];
    }

    /**
     * Get valid types
     * @return array
     */
    public static function getValidTypes() {
        return [
            self::TYPE_CUSTOM,
            self::TYPE_INTRO,
            self::TYPE_SUMMARY,
            self::TYPE_RISK_MATRIX,
            self::TYPE_REDUCED_VULNERABILITY_LIST,
            self::TYPE_VULNERABILITIES,
            self::TYPE_INFO_CHECKS,
            self::TYPE_APPENDIX,
            self::TYPE_ATTACHMENTS,
            self::TYPE_CHART_SECURITY_LEVEL,
            self::TYPE_CHART_VULNERABILITY_DISTRIBUTION,
            self::TYPE_CHART_DEGREE_OF_FULFILLMENT,
        ];
    }

    /**
     * Check if section type is chart
     * @param $section
     * @return bool
     */
    public static function isChart($section) {
        return in_array($section, self::getChartTypes());
    }
}
