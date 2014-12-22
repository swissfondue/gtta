<?php

/**
 * Migration m141222_081949_mail_settings
 */
class m141222_081949_mail_settings extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            'system',
            'email',
            'VARCHAR (1000)'
        );
        $this->addColumn(
            'system',
            'mail_max_attempts',
            'BIGINT'
        );
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn('system', 'email');
        $this->dropColumn('system', 'mail_max_attempts');
		return true;
	}
}