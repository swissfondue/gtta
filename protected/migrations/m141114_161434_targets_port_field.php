<?php

/**
 * Migration m141114_161434_targets_port_field
 */
class m141114_161434_targets_port_field extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            'targets',
            'port',
            'integer'
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn('targets', 'port');
		return true;
	}
}