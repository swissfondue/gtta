<?php

/**
 * Migration m141227_192127_php_resque
 */
class m141227_192127_php_resque extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        // TargetChecks
        $this->dropColumn("target_checks", "pid");
        $this->dropColumn("target_checks", "started");

        // ProjectGtChecks
        $this->dropColumn("project_gt_checks", "pid");
        $this->dropColumn("project_gt_checks", "started");

        // Packages
        $this->dropColumn("packages", "modified");

        // System
        $this->dropColumn("system", "pid");

        // Emails
        $this->dropTable("emails");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        // TargetChecks
        $this->addColumn(
            "target_checks",
            "pid",
            "bigint"
        );
        $this->addColumn(
            "target_checks",
            "started",
            "timestamp without time zone"
        );

        // ProjectGtChecks
        $this->addColumn(
            "project_gt_checks",
            "pid",
            "bigint"
        );
        $this->addColumn(
            "project_gt_checks",
            "started",
            "timestamp without time zone"
        );

        // Packages
        $this->addColumn("packages", "modified", "boolean NOT NULL DEFAULT FALSE");

        // System
        $this->addColumn(
            "system",
            "pid",
            "bigint"
        );

        // Emails
        $this->createTable("emails", array(
            "id" => "bigserial NOT NULL",
            "user_id" => "bigint NOT NULL",
            "subject" => "varchar (1000) NOT NULL",
            "content" => "varchar NOT NULL",
            "attempts" => "integer NOT NULL DEFAULT 0",
            "sent" => "boolean NOT NULL DEFAULT FALSE",
        ));
        $this->addForeignKey(
            "emails_user_id_fkey",
            "emails",
            "user_id",
            "users",
            "id",
            "CASCADE",
            "CASCADE"
        );

		return true;
	}
}