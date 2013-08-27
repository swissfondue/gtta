<?php

/**
 * Migration m130817_141257_3
 */
class m130817_141257_3 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "status", "integer NOT NULL DEFAULT 0");
        $this->addColumn("system", "update_pid", "bigint");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
		$this->dropColumn("system", "status");
        $this->dropColumn("system", "update_pid");

		return true;
	}
}