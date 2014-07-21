<?php

/**
 * Migration m140720_002433_40
 */
class m140720_002433_40 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("check_categories", "status", "integer NOT NULL DEFAULT 1");
        $this->addColumn("check_controls", "status", "integer NOT NULL DEFAULT 1");
        $this->addColumn("references", "status", "integer NOT NULL DEFAULT 1");
        $this->dropColumn("checks", "external_control_id");
        $this->dropColumn("checks", "external_reference_id");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("check_categories", "status");
        $this->dropColumn("check_controls", "status");
        $this->dropColumn("references", "status");
        $this->addColumn("checks", "external_control_id", "bigint");
        $this->addColumn("checks", "external_reference_id", "bigint");
        
		return true;
	}
}