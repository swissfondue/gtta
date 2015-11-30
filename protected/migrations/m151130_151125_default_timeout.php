<?php

/**
 * Migration m151130_151125_default_timeout
 */
class m151130_151125_default_timeout extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->execute("ALTER TABLE packages ALTER COLUMN timeout SET DEFAULT 86400");
        $this->execute("ALTER TABLE target_check_scripts ALTER COLUMN timeout SET DEFAULT 86400"); 

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->execute("ALTER TABLE packages ALTER COLUMN timeout SET DEFAULT 60");
        $this->execute("ALTER TABLE target_check_scripts ALTER COLUMN timeout SET DEFAULT 60");

		return true;
	}
}