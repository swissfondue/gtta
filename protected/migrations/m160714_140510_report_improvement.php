<?php

/**
 * Migration m160714_140510_report_improvement
 */
class m160714_140510_report_improvement extends CDbMigration {

    // report_templates table's columns
    private $_columns = [
        ReportTemplateSection::TYPE_INTRO => "intro",
        ReportTemplateSection::TYPE_SECTION_SECURITY_LEVEL => "security_level_intro",
        ReportTemplateSection::TYPE_SECTION_VULN_DISTR => "vuln_distribution_intro",
        ReportTemplateSection::TYPE_SECTION_DEGREE => "degree_intro",
        ReportTemplateSection::TYPE_RISK_MATRIX => "risk_intro",
        ReportTemplateSection::TYPE_REDUCED_VULN_LIST => "reduced_intro",
        ReportTemplateSection::TYPE_VULNS => "vulns_intro",
        ReportTemplateSection::TYPE_INFO_CHECKS_INTRO => "info_checks_intro",
        ReportTemplateSection::TYPE_APPENDIX => "appendix",
        ReportTemplateSection::TYPE_FOOTER => "footer",
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

        $this->createTable(
            "report_template_sections",
            [
                "id" => "bigserial NOT NULL",
                "report_template_id" => "bigint NOT NULL",
                "type" => "bigint NOT NULL",
                "title" => "varchar(1000)",
                "content" => "text",
                "order" => "bigint NOT NULL DEFAULT 0",
                "report_template_chart_section_id" => "bigint",
                "PRIMARY KEY (id, report_template_id)"
            ]
        );

        $this->createTable(
            "report_template_sections_l10n",
            [
                "report_template_section_id" => "bigserial NOT NULL",
                "language_id" => "bigint NOT NULL",
                "title" => "varchar(1000)",
                "content" => "text",
            ]
        );

        $conn = Yii::app()->db;
        $data = $conn->createCommand("SELECT * FROM report_templates")->queryAll();
        $languages = $conn->createCommand("SELECT * FROM languages")->queryAll();

        foreach ($data as $template) {
            foreach (array_keys($this->_columns) as $section) {
                $this->execute(
                    "INSERT INTO report_template_sections (report_template_id, type, title, content, \"order\")
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
                        "title" => ReportTemplateSection::$titles[$section],
                    ]
                );

                $newSection = $conn->createCommand()
                    ->select("id")
                    ->from("report_template_sections")
                    ->where("report_template_id = :id AND title = :title", [
                        "id" => $template["id"],
                        "title" => ReportTemplateSection::$titles[$section]
                    ])->queryRow();

                foreach ($languages as $language) {
                    $this->execute(
                        "INSERT INTO report_template_sections_l10n (report_template_section_id, language_id, title, content)
                         (
                             SELECT
                                 :section_id,
                                 :language_id,
                                 :title,
                                 (SELECT {$this->_columns[$section]}::TEXT FROM report_templates_l10n WHERE report_template_id = :template_id AND language_id = :language_id)
                         )
                        ",
                        [
                            "section_id" => $newSection["id"],
                            "language_id" => $language  ["id"],
                            "template_id" => $template["id"],
                            "title" => ReportTemplateSection::$titles[$section],
                        ]
                    );
                }
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
        $this->dropTable("report_template_sections_l10n");
        $this->execute("ALTER TABLE report_template_vuln_sections RENAME TO report_template_sections");
        $this->execute("ALTER TABLE report_template_vuln_sections_l10n RENAME TO report_template_sections_l10n");

        $this->execute("ALTER TABLE report_template_sections_l10n RENAME COLUMN report_template_vuln_section_id TO report_template_section_id");

		return true;
	}
}