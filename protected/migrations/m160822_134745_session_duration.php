<?php

/**
 * Migration m160822_134745_session_duration
 */
class m160822_134745_session_duration extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("users", "session_duration", "bigint NOT NULL DEFAULT 1");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("users", "session_duration");

		return true;
	}
}