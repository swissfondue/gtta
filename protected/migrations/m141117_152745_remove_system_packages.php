<?php

/**
 * Migration m141117_152745_remove_system_packages
 */
class m141117_152745_remove_system_packages extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->dropColumn("packages", "system");
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn("packages", "system", "boolean NOT NULL DEFAULT FALSE");
		return true;
	}
}