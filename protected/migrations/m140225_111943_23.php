<?php

/**
 * Migration m140225_111943_23
 */
class m140225_111943_23 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable("project_planner", array(
            "id" => "bigserial NOT NULL",
            "user_id" => "bigint NOT NULL",
            "target_id" => "bigint",
            "check_category_id" => "bigint",
            "project_id" => "bigint",
            "gt_module_id" => "bigint",
            "start_date" => "date NOT NULL",
            "end_date" => "date NOT NULL",
            "finished" => "float NOT NULL DEFAULT 0",
            "PRIMARY KEY (id)",
            "UNIQUE(user_id, project_id, target_id, check_category_id, gt_module_id)"
        ));

        $this->addForeignKey(
            "project_planner_user_id_fkey",
            "project_planner",
            "user_id",
            "users",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "project_planner_target_id_check_category_id_fkey",
            "project_planner",
            "target_id, check_category_id",
            "target_check_categories",
            "target_id, check_category_id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "project_planner_project_id_gt_module_id_fkey",
            "project_planner",
            "project_id, gt_module_id",
            "project_gt_modules",
            "project_id, gt_module_id",
            "CASCADE",
            "CASCADE"
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropForeignKey("project_planner_user_id_fkey", "project_planner");
        $this->dropForeignKey("project_planner_target_id_check_category_id_fkey", "project_planner");
        $this->dropForeignKey("project_planner_project_id_gt_module_id_fkey", "project_planner");
        $this->dropTable("project_planner");

		return true;
	}
}