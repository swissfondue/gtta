<?php

/**
 * Migration m141112_103518_target_checks_run_scripts_column_add
 */
class m141112_103518_target_checks_run_scripts_column_add extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            'target_checks',
            'scripts_to_start',
            'integer array[1] default \'{}\''
        );
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn('target_checks', 'scripts_to_start');
		return true;
	}
}