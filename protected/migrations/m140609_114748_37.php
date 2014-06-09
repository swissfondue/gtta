<?php

/**
 * Migration m140609_114748_37
 */
class m140609_114748_37 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->renameColumn("system", "pid", "update_pid");
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->renameColumn("system", "update_pid", "pid");
		return true;
	}
}