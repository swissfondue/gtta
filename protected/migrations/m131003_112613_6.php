<?php

/**
 * Migration m131003_112613_6
 */
class m131003_112613_6 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->alterColumn("report_template_summary", "rating_from", "NUMERIC(11,2)");
        $this->alterColumn("report_template_summary", "rating_to", "NUMERIC(11,2)");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->alterColumn("report_template_summary", "rating_from", "NUMERIC(3,2)");
        $this->alterColumn("report_template_summary", "rating_to", "NUMERIC(3,2)");

		return true;
	}
}