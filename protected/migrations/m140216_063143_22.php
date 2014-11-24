<?php

/**
 * Migration m140216_063143_22
 */
class m140216_063143_22 extends CDbMigration {
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
            "name" => "shell",
            "type" => Package::TYPE_SCRIPT,
            "version" => "1.10",
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
            "name" => "shell",
            "type" => Package::TYPE_SCRIPT
        ));

		return true;
	}
}