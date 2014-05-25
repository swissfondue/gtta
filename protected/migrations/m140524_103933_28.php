<?php

/**
 * Migration m140524_103933_28
 */
class m140524_103933_28 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->dropForeignKey("target_custom_checks_check_control_id_fkey", "target_custom_checks");
        $this->dropForeignKey("target_custom_checks_target_id_fkey", "target_custom_checks");
        $this->dropForeignKey("target_custom_checks_user_id_fkey", "target_custom_checks");
        $this->dropTable("target_custom_checks");

        $this->createTable("target_custom_checks", array(
            "id" => "bigserial NOT NULL",
            "target_id" => "bigint NOT NULL",
            "check_control_id" => "bigint NOT NULL",
            "user_id" => "bigint NOT NULL",
            "name" => "character varying(1000) DEFAULT NULL",
            "background_info" => "character varying DEFAULT NULL",
            "question" => "character varying DEFAULT NULL",
            "result" => "character varying DEFAULT NULL",
            "solution_title" => "character varying(1000) DEFAULT NULL",
            "solution" => "character varying DEFAULT NULL",
            "reference" => "integer NOT NULL DEFAULT 1",
            "rating" => "integer NOT NULL DEFAULT 0",
            "PRIMARY KEY (id)",
        ));

        $this->addForeignKey(
            "target_custom_checks_target_id_fkey",
            "target_custom_checks",
            "target_id",
            "targets",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_custom_checks_check_control_id_fkey",
            "target_custom_checks",
            "check_control_id",
            "check_controls",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_custom_checks_user_id_fkey",
            "target_custom_checks",
            "user_id",
            "users",
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
        $this->dropTable("target_custom_checks");

        $this->createTable("target_custom_checks", array(
            "target_id" => "bigint NOT NULL",
            "check_control_id" => "bigint NOT NULL",
            "user_id" => "bigint NOT NULL",
            "name" => "character varying(1000) DEFAULT NULL",
            "background_info" => "character varying DEFAULT NULL",
            "question" => "character varying DEFAULT NULL",
            "result" => "character varying DEFAULT NULL",
            "solution_title" => "character varying(1000) DEFAULT NULL",
            "solution" => "character varying DEFAULT NULL",
            "reference" => "integer NOT NULL DEFAULT 1",
            "rating" => "integer NOT NULL DEFAULT 0",
            "PRIMARY KEY (target_id, check_control_id)",
        ));

        $this->addForeignKey(
            "target_custom_checks_target_id_fkey",
            "target_custom_checks",
            "target_id",
            "targets",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_custom_checks_check_control_id_fkey",
            "target_custom_checks",
            "check_control_id",
            "check_controls",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_custom_checks_user_id_fkey",
            "target_custom_checks",
            "user_id",
            "users",
            "id",
            "CASCADE",
            "CASCADE"
        );

		return true;
	}
}