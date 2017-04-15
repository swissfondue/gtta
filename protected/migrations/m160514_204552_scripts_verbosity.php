<?php

/**
 * Migration m160514_204552_scripts_verbosity
 */
class m160514_204552_scripts_verbosity extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "scripts_verbosity", "boolean NOT NULL DEFAULT 't'");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("system", "scripts_verbosity");

		return true;
	}
}