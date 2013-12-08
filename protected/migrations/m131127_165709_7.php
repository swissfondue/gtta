<?php

/**
 * Migration m131127_165709_7
 */
class m131127_165709_7 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable("packages", array(
            "id" => "bigserial NOT NULL",
            "file_name" => "character varying(1000)",
            "type" => "integer NOT NULL DEFAULT 0",
            "system" => "boolean NOT NULL DEFAULT 'f'",
            "version" => "character varying(1000) NOT NULL",
            "name" => "character varying(1000) NOT NULL",
            "status" => "integer NOT NULL DEFAULT 0",
            "PRIMARY KEY (id)",
            "UNIQUE(type, name)"
        ));

        $this->createTable("package_dependencies", array(
            "from_package_id" => "bigint NOT NULL",
            "to_package_id" => "bigint NOT NULL",
            "PRIMARY KEY (from_package_id, to_package_id)",
            "FOREIGN KEY (from_package_id) REFERENCES packages (id) ON UPDATE CASCADE ON DELETE CASCADE",
            "FOREIGN KEY (to_package_id) REFERENCES packages (id) ON UPDATE CASCADE ON DELETE CASCADE",
        ));

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropTable("package_dependencies");
        $this->dropTable("packages");

		return true;
	}
}