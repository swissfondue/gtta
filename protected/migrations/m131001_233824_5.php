<?php

/**
 * Migration m131001_233824_5
 */
class m131001_233824_5 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "report_low_pedestal", "numeric(11,2) NOT NULL DEFAULT 1.00");
        $this->addColumn("system", "report_med_pedestal", "numeric(11,2) NOT NULL DEFAULT 3.00");
        $this->addColumn("system", "report_high_pedestal", "numeric(11,2) NOT NULL DEFAULT 6.00");
        $this->addColumn("system", "report_max_rating", "numeric(11,2) NOT NULL DEFAULT 10.00");
        $this->addColumn("system", "report_med_damping_low", "numeric(11,2) NOT NULL DEFAULT 0.50");
        $this->addColumn("system", "report_high_damping_low", "numeric(11,2) NOT NULL DEFAULT 0.25");
        $this->addColumn("system", "report_high_damping_med", "numeric(11,2) NOT NULL DEFAULT 0.50");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("system", "report_low_pedestal");
        $this->dropColumn("system", "report_med_pedestal");
        $this->dropColumn("system", "report_high_pedestal");
        $this->dropColumn("system", "report_max_rating");
        $this->dropColumn("system", "report_med_damping_low");
        $this->dropColumn("system", "report_high_damping_low");
        $this->dropColumn("system", "report_high_damping_med");
        
		return true;
	}
}