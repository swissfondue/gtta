<?php

/**
 * Migration m160714_140510_report_improvement
 */
class m160714_140510_report_improvement extends CDbMigration {
    // report_templates table's columns
    private $_columns = [
        ReportSection::TYPE_INTRO => "intro",
        ReportSection::TYPE_CHART_SECURITY_LEVEL => "security_level_intro",
        ReportSection::TYPE_CHART_VULN_DISTR => "vuln_distribution_intro",
        ReportSection::TYPE_CHART_VULN_DEGREE => "degree_intro",
        ReportSection::TYPE_RISK_MATRIX => "risk_intro",
        ReportSection::TYPE_REDUCED_VULN_LIST => "reduced_intro",
        ReportSection::TYPE_VULNS => "vulns_intro",
        ReportSection::TYPE_INFO_CHECKS => "info_checks_intro",
        ReportSection::TYPE_APPENDIX => "appendix",
    ];

    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        // rename existing report_template_sections table
        $this->execute("ALTER TABLE report_template_sections RENAME TO report_template_vuln_sections;");
        $this->execute("ALTER TABLE report_template_sections_l10n RENAME TO report_template_vuln_sections_l10n;");
        $this->execute("ALTER TABLE report_template_vuln_sections_l10n RENAME COLUMN report_template_section_id TO report_template_vuln_section_id");

        $this->createTable("report_template_sections", [
            "id" => "bigserial NOT NULL",
            "report_template_id" => "bigint NOT NULL REFERENCES report_templates (id) ON UPDATE CASCADE ON DELETE CASCADE",
            "type" => "int NOT NULL",
            "title" => "varchar(1000)",
            "content" => "text",
            "sort_order" => "int NOT NULL DEFAULT 0",
            "PRIMARY KEY (id)"
        ]);

        $this->createTable("project_report_sections", [
            "id" => "bigserial NOT NULL",
            "project_id" => "bigint NOT NULL REFERENCES projects (id) ON UPDATE CASCADE ON DELETE CASCADE",
            "type" => "int NOT NULL",
            "title" => "varchar(1000)",
            "content" => "text",
            "sort_order" => "int NOT NULL DEFAULT 0",
            "PRIMARY KEY (id)"
        ]);

        $conn = Yii::app()->db;
        $data = $conn->createCommand("SELECT * FROM report_templates")->queryAll();

        foreach ($data as $template) {
            foreach (array_keys($this->_columns) as $section) {
                $this->execute(
                    "INSERT INTO report_template_sections (report_template_id, type, title, content, sort_order)
                     (
                         SELECT
                             :id,
                             :type,
                             :title,
                             (SELECT {$this->_columns[$section]}::TEXT FROM report_templates WHERE id = :id),
                             (SELECT COUNT(*) FROM report_template_sections WHERE report_template_id = :id)
                     )
                    ",
                    [
                        "id" => $template["id"],
                        "type" => $section,
                        "title" => ReportSection::getTypeTitles()[$section],
                    ]
                );
            }
        }

        // drop report_templates table's columns
        foreach ($this->_columns as $column) {
            $this->dropColumn("report_templates", $column);
            $this->dropColumn("report_templates_l10n", $column);
        }

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        foreach ($this->_columns as $c) {
            $this->addColumn("report_templates", $c, "text");
            $this->addColumn("report_templates_l10n", $c, "text");
        }

        $this->dropTable("report_template_sections");
        $this->dropTable("project_report_sections");

        $this->execute("ALTER TABLE report_template_vuln_sections RENAME TO report_template_sections");
        $this->execute("ALTER TABLE report_template_vuln_sections_l10n RENAME TO report_template_sections_l10n");
        $this->execute("ALTER TABLE report_template_sections_l10n RENAME COLUMN report_template_vuln_section_id TO report_template_section_id");

		return true;
	}
}