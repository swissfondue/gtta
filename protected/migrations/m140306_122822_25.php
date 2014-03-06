<?php

/**
 * Migration m140306_122822_25
 */
class m140306_122822_25 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "demo_check_limit", "bigint NOT NULL DEFAULT 0");
        $this->update("system", array("demo_check_limit" => 40));

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("system", "demo_check_limit");
		return true;
	}
}