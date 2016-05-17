<?php

/**
 * Migration m160516_193144_editable_check_fields
 */
class m160516_193144_editable_check_fields extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable(
            "check_fields",
            [
                "id" => "bigserial NOT NULL",
                "check_id" => "bigserial NOT NULL",
                "project_only" => "boolean NOT NULL DEFAULT 'f'",
                "name" => "text NOT NULL",
                "title" => "text NOT NULL",
                "content" => "text",
                "order" => "integer NOT NULL"
            ]
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, name, title, content, \"order\")
             (SELECT id, 'background_info', 'Background Info', background_info, 0 FROM checks);"
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, name, title, content, \"order\")
             (SELECT id, 'question', 'Question', question, 1 FROM checks);"
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, name, title, content, \"order\")
             (SELECT id, 'hints', 'Hints', hints, 2 FROM checks);"
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, project_only, name, title, \"order\")
             (SELECT id, 't', 'result', 'Result', 2 FROM checks);"
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropTable("check_fields");

		return true;
	}
}