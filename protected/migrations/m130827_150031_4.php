<?php

/**
 * Migration m130827_150031_4
 */
class m130827_150031_4 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addForeignKey(
            "target_check_attachments_target_id_check_id_fkey",
            "target_check_attachments",
            "target_id, check_id",
            "target_checks",
            "target_id, check_id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_check_solutions_target_id_check_id_fkey",
            "target_check_solutions",
            "target_id, check_id",
            "target_checks",
            "target_id, check_id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_check_vulns_target_id_check_id_fkey",
            "target_check_vulns",
            "target_id, check_id",
            "target_checks",
            "target_id, check_id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "project_gt_check_attachments_project_id_gt_check_id_fkey",
            "project_gt_check_attachments",
            "project_id, gt_check_id",
            "project_gt_checks",
            "project_id, gt_check_id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "project_gt_check_solutions_project_id_gt_check_id_fkey",
            "project_gt_check_solutions",
            "project_id, gt_check_id",
            "project_gt_checks",
            "project_id, gt_check_id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "project_gt_check_vulns_project_id_gt_check_id_fkey",
            "project_gt_check_vulns",
            "project_id, gt_check_id",
            "project_gt_checks",
            "project_id, gt_check_id",
            "CASCADE",
            "CASCADE"
        );

        $this->execute("ALTER TABLE project_gt_checks ALTER COLUMN status SET DEFAULT 'open'::check_status");
        $this->execute("UPDATE project_gt_checks SET status = 'open'::check_status WHERE status IS NULL");
        $this->execute("ALTER TABLE project_gt_checks ALTER COLUMN status SET NOT NULL");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
		$this->dropForeignKey("target_check_attachments_target_id_check_id_fkey", "target_check_attachments");
        $this->dropForeignKey("target_check_solutions_target_id_check_id_fkey", "target_check_solutions");
        $this->dropForeignKey("target_check_vulns_target_id_check_id_fkey", "target_check_vulns");
        $this->dropForeignKey("project_gt_check_attachments_project_id_gt_check_id_fkey", "project_gt_check_attachments");
        $this->dropForeignKey("project_gt_check_solutions_project_id_gt_check_id_fkey", "project_gt_check_solutions");
        $this->dropForeignKey("project_gt_check_vulns_project_id_gt_check_id_fkey", "project_gt_check_vulns");

        $this->execute("ALTER TABLE project_gt_checks ALTER COLUMN status DROP DEFAULT");
        $this->execute("ALTER TABLE project_gt_checks ALTER COLUMN status DROP NOT NULL");

		return true;
	}
}