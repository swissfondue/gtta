<?php

/**
 * Migration m140205_105423_18
 */
class m140205_105423_18 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("report_templates", "none_description", "character varying");
        $this->addColumn("report_templates", "no_vuln_description", "character varying");
        $this->addColumn("report_templates", "info_description", "character varying");
        $this->addColumn("report_templates_l10n", "none_description", "character varying");
        $this->addColumn("report_templates_l10n", "no_vuln_description", "character varying");
        $this->addColumn("report_templates_l10n", "info_description", "character varying");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("report_templates", "none_description");
        $this->dropColumn("report_templates", "no_vuln_description");
        $this->dropColumn("report_templates", "info_description");
        $this->dropColumn("report_templates_l10n", "none_description");
        $this->dropColumn("report_templates_l10n", "no_vuln_description");
        $this->dropColumn("report_templates_l10n", "info_description");

		return true;
	}
}