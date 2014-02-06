<?php

/**
 * Migration m140205_134614_19
 */
class m140205_134614_19 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("target_checks", "solution", "character varying DEFAULT NULL");
        $this->addColumn("target_checks", "solution_title", "character varying(1000) DEFAULT NULL");
        $this->addColumn("project_gt_checks", "solution", "character varying DEFAULT NULL");
        $this->addColumn("project_gt_checks", "solution_title", "character varying(1000) DEFAULT NULL");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("target_checks", "solution");
        $this->dropColumn("target_checks", "solution_title");
        $this->dropColumn("project_gt_checks", "solution");
        $this->dropColumn("project_gt_checks", "solution_title");

		return true;
	}
}