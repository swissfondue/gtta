<?php

/**
 * Migration m150304_124148_multiple_targets
 */
class m150304_124148_multiple_targets extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->alterColumn(
            "target_checks",
            "override_target",
            "varchar"
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->alterColumn(
            "target_checks",
            "override_target",
            "varchar(1000)"
        );

		return true;
	}
}