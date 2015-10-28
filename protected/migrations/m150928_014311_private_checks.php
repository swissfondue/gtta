<?php

/**
 * Migration m150928_014311_private_checks
 */
class m150928_014311_private_checks extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("checks", "private", "boolean NOT NULL DEFAULT 'f'");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("checks", "private");

		return true;
	}
}