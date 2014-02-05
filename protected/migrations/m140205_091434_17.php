<?php

/**
 * Migration m140205_091434_17
 */
class m140205_091434_17 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        // checks
        $this->addColumn("target_checks", "rating_new", "integer NOT NULL DEFAULT 0");
        $this->update("target_checks", array("rating_new" => 20), "rating = 'hidden'");
        $this->update("target_checks", array("rating_new" => 50), "rating = 'info'");
        $this->update("target_checks", array("rating_new" => 100), "rating = 'low_risk'");
        $this->update("target_checks", array("rating_new" => 200), "rating = 'med_risk'");
        $this->update("target_checks", array("rating_new" => 500), "rating = 'high_risk'");
        $this->dropColumn("target_checks", "rating");
        $this->renameColumn("target_checks", "rating_new", "rating");

        // project GT checks
        $this->addColumn("project_gt_checks", "rating_new", "integer NOT NULL DEFAULT 0");
        $this->update("project_gt_checks", array("rating_new" => 20), "rating = 'hidden'");
        $this->update("project_gt_checks", array("rating_new" => 50), "rating = 'info'");
        $this->update("project_gt_checks", array("rating_new" => 100), "rating = 'low_risk'");
        $this->update("project_gt_checks", array("rating_new" => 200), "rating = 'med_risk'");
        $this->update("project_gt_checks", array("rating_new" => 500), "rating = 'high_risk'");
        $this->dropColumn("project_gt_checks", "rating");
        $this->renameColumn("project_gt_checks", "rating_new", "rating");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        // checks
        $this->addColumn("target_checks", "rating_new", "check_rating DEFAULT NULL");
        $this->update("target_checks", array("rating_new" => "hidden"), "rating = '20'");
        $this->update("target_checks", array("rating_new" => "info"), "rating = '50'");
        $this->update("target_checks", array("rating_new" => "low_risk"), "rating = '100'");
        $this->update("target_checks", array("rating_new" => "med_risk"), "rating = '200'");
        $this->update("target_checks", array("rating_new" => "high_risk"), "rating = '500'");
        $this->dropColumn("target_checks", "rating");
        $this->renameColumn("target_checks", "rating_new", "rating");

        // project GT checks
        $this->addColumn("project_gt_checks", "rating_new", "check_rating DEFAULT NULL");
        $this->update("project_gt_checks", array("rating_new" => "hidden"), "rating = '20'");
        $this->update("project_gt_checks", array("rating_new" => "info"), "rating = '50'");
        $this->update("project_gt_checks", array("rating_new" => "low_risk"), "rating = '100'");
        $this->update("project_gt_checks", array("rating_new" => "med_risk"), "rating = '200'");
        $this->update("project_gt_checks", array("rating_new" => "high_risk"), "rating = '500'");
        $this->dropColumn("project_gt_checks", "rating");
        $this->renameColumn("project_gt_checks", "rating_new", "rating");

		return true;
	}
}