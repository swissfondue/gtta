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
            "global_check_fields",
            [
                "id" => "bigserial NOT NULL",
                "type" => "bigint NOT NULL",
                "name" => "text NOT NULL",
                "title" => "text NOT NULL",
                "hidden" => "boolean NOT NULL DEFAULT 'f'",
                "PRIMARY KEY (id)"
            ]
        );

        $this->createTable(
            "global_check_fields_l10n",
            [
                "global_check_field_id" => "bigint NOT NULL",
                "language_id" => "bigint NOT NULL",
                "title" => "text NOT NULL",
                "PRIMARY KEY (global_check_field_id, language_id)"
            ]
        );
        $this->addForeignKey(
            "global_check_fields_l10n_global_check_field_id_fkey",
            "global_check_fields_l10n",
            "global_check_field_id",
            "global_check_fields",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "global_check_fields_l10n_language_id_fkey",
            "global_check_fields_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->execute(
            "INSERT INTO global_check_fields (type, name, title)
             VALUES
             (:wysiwyg_ro_type, 'background_info', 'Background Info'),
             (:wysiwyg_ro_type, 'question', 'Question'),
             (:wysiwyg_ro_type, 'hints', 'Hints'),
             (:wysiwyg_type, 'result', 'Result')",
            [
                "wysiwyg_ro_type" => GlobalCheckField::TYPE_WYSIWYG_READONLY,
                "wysiwyg_type" => GlobalCheckField::TYPE_WYSIWYG
            ]
        );
        $this->execute(
            "INSERT INTO global_check_fields_l10n (global_check_field_id, language_id, title)
             (
                  SELECT global_check_fields.id, languages.id, global_check_fields.title
                  FROM global_check_fields
                  LEFT JOIN languages ON languages.code = 'en'
             )"
        );

        $this->createTable(
            "check_fields",
            [
                "id" => "bigserial NOT NULL",
                "global_check_field_id" => "bigserial NOT NULL",
                "check_id" => "bigserial NOT NULL",
                "value" => "text",
                "PRIMARY KEY (id)",
                "UNIQUE (global_check_field_id, check_id)"
            ]
        );
        $this->addForeignKey(
            "check_fields_global_check_field_id_fkey",
            "check_fields",
            "global_check_field_id",
            "global_check_fields",
            "id",
            "CASCADE",
            "CASCADE"
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
            "INSERT INTO check_fields (global_check_field_id, check_id, value)
            (
                SELECT global_check_fields.id, checks.id, checks.background_info
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'background_info'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id, value)
            (
                SELECT global_check_fields.id, checks.id, checks.question
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'question'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id, value)
            (
                SELECT global_check_fields.id, checks.id, checks.hints
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'hints'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id, value)
            (
                SELECT global_check_fields.id, checks.id, ''
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'result'
            )"
        );

        $this->createTable(
            "check_fields_l10n",
            [
                "check_field_id" => "bigserial NOT NULL",
                "language_id" => "bigint NOT NULL",
                "value" => "text",
                "PRIMARY KEY (check_field_id, language_id)"
            ]
        );
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
              LEFT JOIN global_check_fields ON global_check_fields.name ='background_info'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, checks_l10n.hints
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'hints'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, checks_l10n.question
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'question'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, ''
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'result'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );

        $this->createTable(
            "target_check_fields",
            [
                "target_check_id" => "bigint NOT NULL",
                "check_field_id" => "bigint NOT NULL",
                "value" => "text",
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
               SELECT target_checks.id, check_fields.id, target_checks.result
               FROM target_checks
               LEFT JOIN global_check_fields ON global_check_fields.name = 'result'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, checks.background_info
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'background_info'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, checks.hints
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'hints'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, checks.question
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'question'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );

        $this->dropColumn("checks", "background_info");
        $this->dropColumn("checks", "question");
        $this->dropColumn("checks", "hints");

        $this->dropColumn("checks_l10n", "background_info");
        $this->dropColumn("checks_l10n", "question");
        $this->dropColumn("checks_l10n", "hints");

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

        $this->addColumn("checks_l10n", "background_info", "text");
        $this->addColumn("checks_l10n", "question", "text");
        $this->addColumn("checks_l10n", "hints", "text");

        $this->addColumn("target_checks", "result", "text");

        //revert original values of `checks` table
        $this->execute(
            "UPDATE checks
             SET background_info = (
                 SELECT check_fields_l10n.value
                 FROM check_fields_l10n
                 LEFT JOIN global_check_fields ON global_check_fields.name = 'background_info'
                 LEFT JOIN check_fields ON check_fields_l10n.check_field_id = check_fields.id AND check_fields.global_check_field_id = global_check_fields.id
                 INNER JOIN languages ON check_fields_l10n.language_id = languages.id AND languages.default
                 WHERE check_fields.check_id = checks.id
             )"
        );

        $this->execute(
            "UPDATE checks
             SET hints = (
                 SELECT check_fields_l10n.value
                 FROM check_fields_l10n
                 LEFT JOIN global_check_fields ON global_check_fields.name = 'hints'
                 LEFT JOIN check_fields ON check_fields_l10n.check_field_id = check_fields.id AND check_fields.global_check_field_id = global_check_fields.id
                 INNER JOIN languages ON check_fields_l10n.language_id = languages.id AND languages.default
                 WHERE check_fields.check_id = checks.id
             )"
        );

        $this->execute(
            "UPDATE checks
             SET question = (
                 SELECT check_fields_l10n.value
                 FROM check_fields_l10n
                 LEFT JOIN global_check_fields ON global_check_fields.name = 'question'
                 LEFT JOIN check_fields ON check_fields_l10n.check_field_id = check_fields.id AND check_fields.global_check_field_id = global_check_fields.id
                 INNER JOIN languages ON check_fields_l10n.language_id = languages.id AND languages.default
                 WHERE check_fields.check_id = checks.id
             )"
        );

        $this->execute(
            "UPDATE checks_l10n
             SET background_info = (
                 SELECT check_fields_l10n.value
                 FROM check_fields_l10n
                 INNER JOIN global_check_fields ON global_check_fields.name = 'background_info'
                 INNER JOIN check_fields ON check_fields.id = check_fields_l10n.check_field_id AND check_fields.check_id = checks_l10n.check_id AND check_fields.global_check_field_id = global_check_fields.id
                 WHERE check_fields_l10n.language_id = checks_l10n.language_id
             )"
        );

        $this->execute(
            "UPDATE checks_l10n
             SET question = (
                 SELECT check_fields_l10n.value
                 FROM check_fields_l10n
                 INNER JOIN global_check_fields ON global_check_fields.name = 'question'
                 INNER JOIN check_fields
                 ON check_fields.id = check_fields_l10n.check_field_id
                 AND check_fields.check_id = checks_l10n.check_id
                 AND check_fields.global_check_field_id = global_check_fields.id
                 WHERE check_fields_l10n.language_id = checks_l10n.language_id
             )"
        );

        $this->execute(
            "UPDATE checks_l10n
             SET hints = (
                 SELECT check_fields_l10n.value
                 FROM check_fields_l10n
                 INNER JOIN global_check_fields ON global_check_fields.name = 'hints'
                 INNER JOIN check_fields
                 ON check_fields.id = check_fields_l10n.check_field_id
                 AND check_fields.check_id = checks_l10n.check_id
                 AND check_fields.global_check_field_id = global_check_fields.id
                 WHERE check_fields_l10n.language_id = checks_l10n.language_id
             )"
        );

        //revert original values of `target_checks` table
        $this->execute(
            "UPDATE target_checks
             SET result =
             (
                 SELECT target_check_fields.value
                 FROM target_check_fields
                 INNER JOIN global_check_fields ON global_check_fields.name = 'result'
                 INNER JOIN check_fields
                 ON check_fields.id = target_check_fields.check_field_id
                 AND check_fields.global_check_field_id = global_check_fields.id
                 WHERE target_check_fields.target_check_id = target_checks.id
             )"
        );

        $this->dropTable("target_check_fields");
        $this->dropTable("check_fields_l10n");
        $this->dropTable("check_fields");
        $this->dropTable("global_check_fields_l10n");
        $this->dropTable("global_check_fields");

		return true;
	}
}