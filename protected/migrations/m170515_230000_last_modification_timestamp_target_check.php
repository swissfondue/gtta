<?php

/**
 * Migration m170515_230000_last_modification_timestamp_target_check.php
 */
class m170515_230000_last_modification_timestamp_target_check extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            'target_checks',
            'last_modified',
            'bigint'
        );
        $this->addColumn(
            'target_custom_checks',
            'last_modified',
            'bigint'
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn('target_checks', 'last_modified');
        $this->dropColumn('target_custom_checks', 'last_modified');
        return true;
	}
}
