<?php

/**
 * Migration m140525_110958_29
 */
class m140525_110958_29 extends CDbMigration {
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