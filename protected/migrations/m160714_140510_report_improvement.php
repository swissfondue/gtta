<?php

/**
 * Migration m160714_140510_report_improvement
 */
class m160714_140510_report_improvement extends CDbMigration {
    // initila sections
    const SECTION_INTRO = "intro";
    const SECTION_SECURITY_LEVEL_INTRO = "security_level_intro";
    const SECTION_VULN_DISTR_INTRO = "vuln_distr_intro";
    const SECTION_DEGREE_INTRO = "fulfillment_intro";
    const RISK_MATRIX_INTRO = "risk_matrix_intro";
    const REDUCED_VULN_LIST_INTRO = "reduced_vuln_list_intro";
    const HIGH_RISK_DESCRIPTION = "high_risk_description";
    const MED_RISK_DESCRIPTION = "med_risk_description";
    const LOW_RISK_DESCRIPTION = "low_risk_description";
    const NO_TEST_DONE_DESCRIPTION = "no_test_done_description";
    const NO_VULN_DESCRIPTION = "no_vuln_risk_description";
    const INFO_DESCRIPTION = "info_description";
    const VULNS_INTRO= "vulns_intro";
    const INFO_CHECKS_INTRO = "info_checks_intro";
    const APPENDIX = "appendix";
    const FOOTER = "footer";

    // section list
    private $_sections = [
        self::SECTION_INTRO,
        self::SECTION_SECURITY_LEVEL_INTRO,
        self::SECTION_VULN_DISTR_INTRO,
        self::SECTION_DEGREE_INTRO,
        self::RISK_MATRIX_INTRO,
        self::REDUCED_VULN_LIST_INTRO,
        self::HIGH_RISK_DESCRIPTION,
        self::MED_RISK_DESCRIPTION,
        self::LOW_RISK_DESCRIPTION,
        self::NO_TEST_DONE_DESCRIPTION,
        self::NO_VULN_DESCRIPTION,
        self::INFO_DESCRIPTION,
        self::VULNS_INTRO,
        self::INFO_CHECKS_INTRO,
        self::APPENDIX,
        self::FOOTER,
    ];

    // section titles
    private $titles = [
        self::SECTION_INTRO => "Intro",
        self::SECTION_SECURITY_LEVEL_INTRO => "Security Level Introduction",
        self::SECTION_VULN_DISTR_INTRO => "Vuln Distribution Introduction",
        self::SECTION_DEGREE_INTRO => "Degree of Fulfillment Introduction",
        self::RISK_MATRIX_INTRO => "Risk Matrix Introduction",
        self::REDUCED_VULN_LIST_INTRO => "Reduced Vuln List Introduction",
        self::HIGH_RISK_DESCRIPTION => "High Risk Description",
        self::MED_RISK_DESCRIPTION => "Med Risk Description",
        self::LOW_RISK_DESCRIPTION => "Low Risk Description",
        self::NO_VULN_DESCRIPTION => "No Vulnerability Description",
        self::NO_TEST_DONE_DESCRIPTION => "No Test Done Description",
        self::INFO_DESCRIPTION => "Info Description",
        self::VULNS_INTRO => "Vulns Introduction",
        self::INFO_CHECKS_INTRO => "Info Checks Introduction",
        self::APPENDIX => "Appendix",
        self::FOOTER => "Footer",
    ];

    // report_templates table's columns
    private $columns = [
        self::SECTION_INTRO => "intro",
        self::SECTION_SECURITY_LEVEL_INTRO => "security_level_intro",
        self::SECTION_VULN_DISTR_INTRO => "vuln_distribution_intro",
        self::SECTION_DEGREE_INTRO => "degree_intro",
        self::RISK_MATRIX_INTRO => "risk_intro",
        self::REDUCED_VULN_LIST_INTRO => "reduced_intro",
        self::HIGH_RISK_DESCRIPTION => "high_description",
        self::MED_RISK_DESCRIPTION => "med_description",
        self::LOW_RISK_DESCRIPTION => "low_description",
        self::NO_TEST_DONE_DESCRIPTION => "none_description",
        self::NO_VULN_DESCRIPTION => "no_vuln_description",
        self::INFO_DESCRIPTION => "info_description",
        self::VULNS_INTRO => "vulns_intro",
        self::INFO_CHECKS_INTRO => "info_checks_intro",
        self::APPENDIX => "appendix",
        self::FOOTER => "footer"
    ];

    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        // rename existing report_template_sections table
        $this->execute("ALTER TABLE report_template_sections RENAME TO report_template_vuln_sections;");

        $this->createTable(
            "report_template_sections",
            [
                "id" => "bigserial NOT NULL",
                "report_template_id" => "bigint NOT NULL",
                "title" => "varchar(1000)",
                "content" => "text",
                "order" => "bigint NOT NULL DEFAULT 0",
                "PRIMARY KEY (id, report_template_id)"
            ]
        );

        $conn = Yii::app()->db;
        $data = $conn->createCommand("SELECT * FROM report_templates")->query();

        foreach ($data as $template) {
            foreach ($this->_sections as $section) {
                $this->execute(
                    "INSERT INTO report_template_sections (report_template_id, title, content, \"order\")
                     (
                         SELECT
                             :id,
                             :title,
                             (SELECT :col::TEXT FROM report_templates WHERE id = :id),
                             (SELECT COUNT(*) FROM report_template_sections WHERE report_template_id = :id)
                     )
                    ",
                    [
                        "id" => $template["id"],
                        "title" => $this->titles[$section],
                        "col" => $this->columns[$section]
                    ]
                );
            }
        }

        // drop report_templates table's columns
        foreach ($this->columns as $column) {
            $this->dropColumn("report_templates", $column);
        }

        //$this->createTable("project_report_template_sections", []);

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        foreach ($this->columns as $c) {
            $this->addColumn(
                "report_templates",
                $c,
                "text"
            );
        }

        $this->dropTable("report_template_sections");
        $this->execute("ALTER TABLE report_template_vuln_sections RENAME TO report_template_sections");

		return true;
	}
}