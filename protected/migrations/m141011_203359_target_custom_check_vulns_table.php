<?php

/**
 * Migration m141011_203359_target_custom_check_vulns_table
 */
class m141011_203359_target_custom_check_vulns_table extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable(
            'target_custom_check_vulns',
            array(
                'target_custom_check_id' => 'bigint PRIMARY KEY',
                'user_id' => 'bigint NOT NULL',
                'deadline' => 'date',
                'status' => 'bigint'
            )
        );

        $this->addForeignKey(
            'target_custom_check_vulns_check_id_fkey',
            'target_custom_check_vulns',
            'target_custom_check_id',
            'target_custom_checks',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'target_custom_check_vulns_user_id_fkey',
            'target_custom_check_vulns',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropTable('target_custom_check_vulns');
		return true;
	}
}