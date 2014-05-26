<?php

/**
 * Migration m140526_000949_30
 */
class m140526_000949_30 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->renameColumn("system", "update_pid", "pid");
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->renameColumn("system", "pid", "update_pid");
		return true;
	}
}