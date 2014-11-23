<?php

/**
 * Migration m140213_020747_20
 */
class m140213_020747_20 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        // skip for new version builds
        $system = $this->getDbConnection()->createCommand("SELECT version FROM system")->query();

        if (!$system->count()) {
            return true;
        }
        
        $this->insert("packages", array(
            "name" => "telnet_banner",
            "type" => Package::TYPE_SCRIPT,
            "version" => "1.9",
            "system" => true,
            "status" => Package::STATUS_INSTALLED
        ));

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->delete("packages", "name = :name AND type = :type AND system", array(
            "name" => "telnet_banner",
            "type" => Package::TYPE_SCRIPT
        ));

		return true;
	}
}