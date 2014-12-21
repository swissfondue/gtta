<?php

/**
 * Migration m141218_102658_modified_packages
 */
class m141218_102658_modified_packages extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            'packages',
            'modified',
            'BOOLEAN NOT NULL DEFAULT FALSE'
        );
        return true;
    }

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn('packages', 'modified');
		return true;
	}
}