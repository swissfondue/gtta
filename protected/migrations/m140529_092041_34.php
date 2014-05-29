<?php

/**
 * Migration m140529_092041_34
 */
class m140529_092041_34 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("target_checks", "poc", "character varying");
        $this->addColumn("target_checks", "links", "character varying");
        $this->addColumn("target_custom_checks", "poc", "character varying");
        $this->addColumn("target_custom_checks", "links", "character varying");
        $this->addColumn("system", "checklist_poc", "boolean NOT NULL DEFAULT 't'");
        $this->addColumn("system", "checklist_links", "boolean NOT NULL DEFAULT 't'");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("target_checks", "poc");
        $this->dropColumn("target_checks", "links");
        $this->dropColumn("target_custom_checks", "poc");
        $this->dropColumn("target_custom_checks", "links");
        $this->dropColumn("system", "checklist_poc");
        $this->dropColumn("system", "checklist_links");

		return true;
	}
}