<?php

/**
 * Migration m150125_201730_packages_timeout
 */
class m150125_201730_packages_timeout extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("packages", "timeout", "BIGINT DEFAULT 60");
        $this->addColumn("target_checks", "timeout", "BIGINT");
        $this->createTable("target_check_scripts", array(
            "target_check_id" => "bigint NOT NULL",
            "check_script_id" => "bigint NOT NULL",
            "start"           => "boolean NOT NULL DEFAULT 't'",
            "timeout"         => "bigint",
            "PRIMARY KEY (target_check_id, check_script_id)"
        ));
        $this->addForeignKey(
            "target_check_scripts_target_check_id_fkey",
            "target_check_scripts",
            "target_check_id",
            "target_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "target_check_scripts_check_script_id_fkey",
            "target_check_scripts",
            "check_script_id",
            "check_scripts",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->dropColumn("target_checks", "scripts_to_start");

        // Filling target_check_scripts with current scripts
        $this->getDbConnection()->createCommand(
            "INSERT INTO target_check_scripts (target_check_id, check_script_id)
                SELECT target_checks.id, check_scripts.id FROM check_scripts
                LEFT JOIN target_checks ON target_checks.check_id = check_scripts.check_id"
        )->query();

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("packages", "timeout");
        $this->dropColumn("target_checks", "timeout");
        $this->addColumn(
            "target_checks",
            "scripts_to_start",
            "integer array[1] DEFAULT '{}'"
        );
        $this->dropTable("target_check_scripts");

		return true;
	}
}