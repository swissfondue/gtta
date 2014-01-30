<?php

/**
 * Migration m140130_082903_14
 */
class m140130_082903_14 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("check_inputs", "visible", "boolean NOT NULL DEFAULT 't'");
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("check_inputs", "visible");
		return true;
	}
}