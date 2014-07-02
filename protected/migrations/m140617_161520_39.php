<?php

/**
 * Migration m140617_161520_39
 */
class m140617_161520_39 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("report_templates", "type", "integer NOT NULL DEFAULT 0");
        $this->addColumn("report_templates", "file_path", "character varying(1000)");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("report_templates", "type");
        $this->dropColumn("report_templates", "file_path");

		return true;
	}
}