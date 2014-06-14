<?php

/**
 * Migration m140614_121742_38
 */
class m140614_121742_38 extends CDbMigration {
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