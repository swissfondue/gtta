<?php

/**
 * Migration m140526_002616_31
 */
class m140526_002616_31 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("projects", "start_date", "date");
        $this->addColumn("projects", "status_new", "integer NOT NULL DEFAULT 0");
        $this->update("projects", array("status_new" => 0), "status = 'open'");
        $this->update("projects", array("status_new" => 10), "status = 'in_progress'");
        $this->update("projects", array("status_new" => 100), "status = 'finished'");
        $this->dropColumn("projects", "status");
        $this->renameColumn("projects", "status_new", "status");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("projects", "start_date");
        $this->addColumn("projects", "status_old", "project_status NOT NULL DEFAULT 'open'");
        $this->update("projects", array("status_old" => "open"), "status = 0");
        $this->update("projects", array("status_old" => "in_progress"), "status = 10");
        $this->update("projects", array("status_old" => "finished"), "status = 100");
        $this->dropColumn("projects", "status");
        $this->renameColumn("projects", "status_old", "status");

		return true;
	}
}