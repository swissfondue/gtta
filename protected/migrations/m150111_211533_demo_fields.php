<?php

/**
 * Migration m150111_211533_demo_fields
 */
class m150111_211533_demo_fields extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->dropColumn("system", "demo");
        $this->dropColumn("system", "demo_check_limit");
        $this->dropColumn("checks", "demo");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn("system", "demo", "boolean NOT NULL DEFAULT 't'");
        $this->addColumn("system", "demo_check_limit", "bigint NOT NULL DEFAULT 0");
        $this->update("system", array("demo_check_limit" => 40));
        $this->addColumn("checks", "demo", "boolean NOT NULL DEFAULT 'f'");

        return true;
	}
}