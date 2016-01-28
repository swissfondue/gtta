<?php

/**
 * Migration m160128_135846_time_tracker_fix
 */
class m160128_135846_time_tracker_fix extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->dropColumn("project_time", "last_action_time");
        $this->execute("ALTER TABLE project_time ALTER COLUMN start_time DROP DEFAULT");
        $this->execute("ALTER TABLE project_time ALTER COLUMN create_time DROP DEFAULT");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn(
            "project_time",
            "last_action_time",
            "timestamp WITHOUT TIME ZONE NOT NULL DEFAULT NOW()"
        );

        $this->execute("ALTER TABLE project_time ALTER COLUMN start_time SET DEFAULT NOW()");
        $this->execute("ALTER TABLE project_time ALTER COLUMN create_time SET DEFAULT NOW()");

		return true;
	}
}