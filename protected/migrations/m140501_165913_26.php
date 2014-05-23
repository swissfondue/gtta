<?php

/**
 * Migration m140501_165913_26
 */
class m140501_165913_26 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("check_categories", "external_id", "bigint DEFAULT NULL");
        $this->addColumn("check_controls", "external_id", "bigint DEFAULT NULL");
        $this->addColumn("checks", "external_id", "bigint DEFAULT NULL");
        $this->addColumn("checks", "create_time", "timestamp WITHOUT TIME ZONE NOT NULL DEFAULT NOW()");
        $this->addColumn("checks", "status", "integer NOT NULL DEFAULT 1");
        $this->addColumn("packages", "external_id", "bigint DEFAULT NULL");
        $this->addColumn("packages", "create_time", "timestamp WITHOUT TIME ZONE NOT NULL DEFAULT NOW()");
        $this->addColumn("references", "external_id", "bigint DEFAULT NULL");
        $this->addColumn("system", "community_min_rating", "numeric(3,2) NOT NULL DEFAULT 0.00");
        $this->addColumn("system", "community_allow_unverified", "boolean NOT NULL DEFAULT 'f'");
        $this->addColumn("system", "integration_key", "character varying(1000) DEFAULT NULL");
        $this->renameColumn("system", "update_pid", "pid");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("check_categories", "external_id");
        $this->dropColumn("check_controls", "external_id");
        $this->dropColumn("checks", "external_id");
        $this->dropColumn("checks", "create_time");
        $this->dropColumn("checks", "status");
        $this->dropColumn("packages", "external_id");
        $this->dropColumn("packages", "create_time");
        $this->dropColumn("references", "external_id");
        $this->dropColumn("system", "community_min_rating");
        $this->dropColumn("system", "community_allow_unverified");
        $this->dropColumn("system", "integration_key");
        $this->renameColumn("system", "pid", "update_pid");
        
		return true;
	}
}