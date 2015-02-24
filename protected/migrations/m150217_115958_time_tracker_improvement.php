<?php

/**
 * Migration m150217_115958_time_tracker_improvement
 */
class m150217_115958_time_tracker_improvement extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        // Time in seconds
        $this->addColumn(
            "project_time",
            "time",
            "bigint"
        );

        // Convert hours to seconds
        $this->execute("UPDATE project_time SET time = hours * 3600");
        $this->addColumn(
            "project_time",
            "start_time",
            "timestamp WITHOUT TIME ZONE NOT NULL DEFAULT NOW()"
        );
        $this->addColumn(
            "project_time",
            "last_action_time",
            "timestamp WITHOUT TIME ZONE NOT NULL DEFAULT NOW()"
        );
        $this->dropColumn("project_time", "hours");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn("project_time", "hours", "numeric(11, 1)");
        $this->execute("UPDATE project_time SET hours = round( time / 3600, 1 )");
        $this->dropColumn("project_time", "time");
        $this->dropColumn("project_time", "start_time");
        $this->dropColumn("project_time", "last_action_time");

		return true;
	}
}