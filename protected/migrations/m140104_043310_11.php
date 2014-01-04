<?php

/**
 * Migration m140104_043310_11
 */
class m140104_043310_11 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "demo", "boolean NOT NULL DEFAULT 't'");
        $this->addColumn("checks", "demo", "boolean NOT NULL DEFAULT 'f'");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("system", "demo");
        $this->dropColumn("checks", "demo");

		return true;
	}
}