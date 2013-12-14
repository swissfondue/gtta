<?php

/**
 * Migration m131212_123741_10
 */
class m131212_123741_10 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->execute("ALTER TABLE check_scripts ALTER COLUMN package_id SET NOT NULL");
        $this->dropColumn("check_scripts", "name");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
		return false;
	}
}