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
                "type" => "bigint NOT NULL",
                "project_only" => "boolean NOT NULL DEFAULT 'f'",
                "name" => "text NOT NULL",
                "title" => "text NOT NULL",
                "value" => "text",
                "sort_order" => "integer NOT NULL",
                "hidden" => "boolean NOT NULL DEFAULT 'f'",
                "PRIMARY KEY (id)"
            ]
        );

        $this->addForeignKey(
            "check_fields_check_id_fkey",
            "check_fields",
            "check_id",
            "checks",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, \"type\", name, title, value, \"sort_order\")
             (SELECT id, :type, 'background_info', 'Background Info', background_info, 0 FROM checks)",
            [
                "type" => CheckField::TYPE_WYSIWYG_READONLY
            ]
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, \"type\", name, title, value, \"sort_order\")
             (SELECT id, :type, 'question', 'Question', question, 1 FROM checks)",
            [
                "type" => CheckField::TYPE_WYSIWYG_READONLY
            ]
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, \"type\", name, title, value, \"sort_order\")
             (SELECT id, :type, 'hints', 'Hints', hints, 2 FROM checks)",
            [
                "type" => CheckField::TYPE_WYSIWYG_READONLY
            ]
        );

        $this->execute(
            "INSERT INTO check_fields (check_id, \"type\", project_only, name, title, \"sort_order\")
             (SELECT id, :type, 't', 'result', 'Result', 3 FROM checks)",
            [
                "type" => CheckField::TYPE_TEXTAREA
            ]
        );

        $this->createTable("check_fields_l10n", [
            "check_field_id" => "bigserial NOT NULL",
            "language_id" => "bigint NOT NULL",
            "value" => "text",
            "PRIMARY KEY (check_field_id, language_id)"
        ]);

        $this->addForeignKey(
            "check_fields_l10n_check_field_id_fkey",
            "check_fields_l10n",
            "check_field_id",
            "check_fields",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "check_fields_l10n_language_id_fkey",
            "check_fields_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, checks_l10n.background_info
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.name = 'background_info'
            )"
        );

        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, checks_l10n.hints
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.name = 'hints'
            )"
        );

        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, checks_l10n.question
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.name = 'question'
            )"
        );

        $this->createTable(
            "target_check_fields",
            [
                "target_check_id" => "bigint NOT NULL",
                "check_field_id" => "bigint NOT NULL",
                "value" => "text",
                "hidden" => "boolean NOT NULL DEFAULT 'f'",
                "PRIMARY KEY (target_check_id, check_field_id)"
            ]
        );

        $this->addForeignKey(
            "target_check_fields_target_check_id_fkey",
            "target_check_fields",
            "target_check_id",
            "target_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_check_fields_check_field_id_fkey",
            "target_check_fields",
            "check_field_id",
            "check_fields",
            "id",
            "CASCADE",
            "CASCADE"
        );


        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, target_checks.result
               FROM target_checks
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id and check_fields.name = 'result'
             )"
        );

        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, check_fields.value
               FROM target_checks
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id and check_fields.name = 'background_info'
             )"
        );

        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, check_fields.value
               FROM target_checks
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id and check_fields.name = 'hints'
             )"
        );

        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, check_fields.value
               FROM target_checks
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id and check_fields.name = 'question'
             )"
        );

        $this->dropColumn("checks", "background_info");
        $this->dropColumn("checks", "question");
        $this->dropColumn("checks", "hints");

        $this->dropColumn("target_checks", "result");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn("checks", "background_info", "text");
        $this->addColumn("checks", "question", "text");
        $this->addColumn("checks", "hints", "text");

        $this->addColumn("target_checks", "result", "text");

        //revert original values of `checks` table
        $this->execute(
            "UPDATE checks
             SET background_info = (SELECT value FROM check_fields WHERE check_id = checks.id AND name = 'background_info');"
        );
        $this->execute(
            "UPDATE checks
             SET hints = (SELECT value FROM check_fields WHERE check_id = checks.id AND name = 'hints');"
        );
        $this->execute(
            "UPDATE checks
             SET question = (SELECT value FROM check_fields WHERE check_id = checks.id AND name = 'question');"
        );

        //revert original values of `target_checks` table
        $this->execute(
            "UPDATE target_checks
             SET result =
             (
                 SELECT target_check_fields.value
                 FROM target_check_fields
                 INNER JOIN check_fields
                 ON check_fields.id = target_check_fields.check_field_id
                 AND target_check_fields.target_check_id = target_checks.id
                 AND check_fields.name = 'result'
             )"
        );

        $this->dropTable("target_check_fields");
        $this->dropTable("check_fields_l10n");
        $this->dropTable("check_fields");

		return true;
	}
}