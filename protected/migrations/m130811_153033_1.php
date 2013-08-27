<?php

/**
 * Migration m130811_153033_1
 */
class m130811_153033_1 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
	public function safeUp() {
        $this->addColumn("system", "workstation_id", "uuid");
        $this->addColumn("system", "workstation_key", "character varying(1000)");
        $this->addColumn("system", "version", "character varying(1000) NOT NULL DEFAULT '0.1'");
        $this->addColumn("system", "version_description", "character varying(1000)");
        $this->addColumn("system", "update_version", "character varying(1000)");
        $this->addColumn("system", "update_description", "character varying(1000)");
        $this->addColumn("system", "update_check_time", "timestamp without time zone");
        $this->addColumn("system", "update_time", "timestamp without time zone");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
	public function safeDown() {
		$this->dropColumn("system", "workstation_id");
        $this->dropColumn("system", "workstation_key");
        $this->dropColumn("system", "version");
        $this->dropColumn("system", "version_description");
        $this->dropColumn("system", "update_version");
        $this->dropColumn("system", "update_description");
        $this->dropColumn("system", "update_check_time");
        $this->dropColumn("system", "update_time");

		return true;
	}
}