<?php

/**
 * Report section class
 */
class ReportSection {
    /**
     * Built-in section types
     */
    const TYPE_INTRO = 10;
    const TYPE_SECTION_SECURITY_LEVEL = 20;
    const TYPE_SECTION_VULN_DISTR = 30;
    const TYPE_SECTION_DEGREE = 40;
    const TYPE_RISK_MATRIX = 50;
    const TYPE_REDUCED_VULN_LIST = 60;
    const TYPE_VULNS = 70;
    const TYPE_INFO_CHECKS_INTRO = 80;
    const TYPE_APPENDIX = 90;
    const TYPE_FOOTER = 100;
    const TYPE_CUSTOM = 200;

    /**
     * Get type titles
     * @return array
     */
    public static function getTypeTitles() {
        return [
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
    }

    /**
     * Get chart types
     * @return array
     */
    public static function getChartTypes() {
        return [
            self::TYPE_SECTION_SECURITY_LEVEL,
            self::TYPE_SECTION_VULN_DISTR,
            self::TYPE_SECTION_DEGREE,
        ];
    }

    /**
     * Get valid types
     * @return array
     */
    public static function getValidTypes() {
        return [
            self::TYPE_INTRO,
            self::TYPE_SECTION_SECURITY_LEVEL,
            self::TYPE_SECTION_VULN_DISTR,
            self::TYPE_SECTION_DEGREE,
            self::TYPE_RISK_MATRIX,
            self::TYPE_REDUCED_VULN_LIST,
            self::TYPE_VULNS,
            self::TYPE_INFO_CHECKS_INTRO,
            self::TYPE_APPENDIX,
            self::TYPE_FOOTER,
            self::TYPE_CUSTOM,
        ];
    }
}
