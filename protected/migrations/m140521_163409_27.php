<?php

/**
 * Migration m140521_163409_27
 */
class m140521_163409_27 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "community_catalogs_cache", "character varying");
        $this->addColumn("checks", "external_control_id", "bigint");
        $this->addColumn("checks", "external_reference_id", "bigint");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("system", "community_catalogs_cache");
        $this->dropColumn("checks", "external_control_id");
        $this->dropColumn("checks", "external_reference_id");

		return true;
	}
}