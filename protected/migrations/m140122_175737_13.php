<?php

/**
 * Migration m140122_175737_13
 */
class m140122_175737_13 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->insert("packages", array(
            "name" => "metasploit",
            "type" => Package::TYPE_SCRIPT,
            "version" => "1.7",
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
            "name" => "metasploit",
            "type" => Package::TYPE_SCRIPT
        ));

		return true;
	}
}