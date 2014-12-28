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
            'varchar (1000)'
        );
        $this->addColumn(
            'system',
            'mail_max_attempts',
            'integer'
        );
        $this->addColumn(
            'system',
            'mail_host',
            'varchar (1000)'
        );
        $this->addColumn(
            'system',
            'mail_port',
            'integer'
        );
        $this->addColumn(
            'system',
            'mail_username',
            'varchar (1000)'
        );
        $this->addColumn(
            'system',
            'mail_password',
            'varchar (1000)'
        );
        $this->addColumn(
            'system',
            'mail_encryption',
            'boolean NOT NULL DEFAULT FALSE'
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
        $this->dropColumn('system', 'mail_host');
        $this->dropColumn('system', 'mail_port');
        $this->dropColumn('system', 'mail_username');
        $this->dropColumn('system', 'mail_password');
        $this->dropColumn('system', 'mail_encryption');
		return true;
	}
}