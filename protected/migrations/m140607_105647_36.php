<?php

/**
 * Migration m140607_105647_36
 */
class m140607_105647_36 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("projects", "hours_allocated", "NUMERIC(11,1) NOT NULL DEFAULT 0.0");
        $this->addColumn("project_users", "hours_allocated", "NUMERIC(11,1) NOT NULL DEFAULT 0.0");
        $this->addColumn("project_users", "hours_spent", "NUMERIC(11,1) NOT NULL DEFAULT 0.0");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("projects", "hours_allocated");
        $this->dropColumn("project_users", "hours_allocated");
        $this->dropColumn("project_users", "hours_spent");

		return true;
	}
}