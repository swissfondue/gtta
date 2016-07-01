<?php

/**
 * Migration m160622_133441_issues
 */
class m160622_133441_issues extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->execute(
            "INSERT INTO global_check_fields (type, name, title) (
                 SELECT type, 'poc', 'PoC' FROM global_check_fields WHERE name = 'result'
             );"
        );
        $this->execute(
            "INSERT INTO global_check_fields_l10n (global_check_field_id, title, language_id) (
                 (
                      SELECT global_check_fields.id, global_check_fields.title, languages.id
                      FROM global_check_fields
                      LEFT JOIN languages on languages.code = 'en'
                      WHERE global_check_fields.name = 'poc'
                      LIMIT 1
                 )
             );"
        );

        $this->createTable(
            "issues",
            [
                "id" => "bigserial NOT NULL",
                "project_id" => "bigint NOT NULL",
                "check_id" => "bigint NOT NULL",
                "name" => "text NOT NULL",
                "PRIMARY KEY (id)"
            ]
        );

        $this->addForeignKey(
            "issues_project_id_fkey",
            "issues",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "issued_check_id_fkey",
            "issues",
            "check_id",
            "checks",
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
        $this->execute("DELETE FROM global_check_fields WHERE name = 'poc';");

        $this->dropTable("issues");

		return true;
	}
}