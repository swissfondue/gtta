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
		return true;
	}
}