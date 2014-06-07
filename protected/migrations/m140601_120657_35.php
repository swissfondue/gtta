<?php

/**
 * Migration m140601_120657_35
 */
class m140601_120657_35 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable("target_custom_check_attachments", array(
            "target_custom_check_id" => "bigint NOT NULL",
            "name" => "character varying(1000)",
            "type" => "character varying(1000)",
            "path" => "character varying(1000)",
            "size" => "bigint NOT NULL DEFAULT 0",
            "PRIMARY KEY (path)",
        ));

        $this->addForeignKey(
            "target_custom_check_attachments_target_custom_check_id_fkey",
            "target_custom_check_attachments",
            "target_custom_check_id",
            "target_custom_checks",
            "id",
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
        $this->dropTable("target_custom_check_attachments");
		return true;
	}
}