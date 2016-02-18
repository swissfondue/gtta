<?php

/**
 * Migration m160217_072906_drop_timestamp_default
 */
class m160217_072906_drop_timestamp_default extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $columns = array(
            "checks.create_time",
            "login_history.create_time",
            "packages.create_time",
            "project_time.create_time"
        );

        foreach ($columns as $column) {
            list($table, $column) = explode(".", $column);
            $this->execute("ALTER TABLE $table ALTER COLUMN $column DROP DEFAULT");
        }

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $columns = array(
            "checks.create_time",
            "login_history.create_time",
            "packages.create_time",
            "project_time.create_time"
        );

        foreach ($columns as $column) {
            list($table, $column) = explode(".", $column);
            $this->execute("ALTER TABLE $table ALTER COLUMN $column SET DEFAULT NOW()");
        }

		return true;
	}
}