<?php

/**
 * Migration m160818_103318_project_custom_report
 */
class m160818_103318_project_custom_report extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("projects", "report_template_id", "int REFERENCES report_templates (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->addColumn("projects", "custom_report", "boolean");
        $this->addColumn("projects", "report_options", "text");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("projects", "report_template_id");
        $this->dropColumn("projects", "custom_report");
        $this->dropColumn("projects", "report_options");

		return true;
	}
}