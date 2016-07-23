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
        // global_check_fields
        $this->execute(
            "INSERT INTO global_check_fields (type, name, title, hidden, value)
             VALUES
             (:text_type, 'application_protocol', 'Application Protocol', 'f', NULL),
             (:radio_type, 'transport_protocol', 'Transport Protocol', 'f', :tp),
             (:text_type, 'port', 'Port', 'f', NULL),
             (:textarea_type, 'override_target', 'Override Target', 'f', NULL),
             (:text_type, 'solution_title', 'Solution Title', 't', NULL),
             (:textarea_type, 'solution', 'Solution', 't', NULL),
             (:textarea_type, 'poc', 'PoC', 'f', NULL)",
            [
                "textarea_type" => GlobalCheckField::TYPE_TEXTAREA,
                "text_type" => GlobalCheckField::TYPE_TEXT,
                "radio_type" => GlobalCheckField::TYPE_RADIO,
                "tp" => json_encode(["TCP", "UDP"])
            ]
        );

        // global_check_fields_l10n
        $this->execute(
            "INSERT INTO global_check_fields_l10n (global_check_field_id, language_id, title, value)
             (
                  SELECT gcf.id, languages.id, gcf.title, gcf.value
                  FROM global_check_fields gcf
                  LEFT JOIN languages ON languages.code = 'en'
                  WHERE (gcf.id, languages.id) NOT IN (
                      SELECT global_check_field_id, language_id
                      FROM global_check_fields_l10n
                  )
             )"
        );

        // check_fields
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id)
            (
                SELECT global_check_fields.id, checks.id
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'application_protocol'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id, value)
            (
                SELECT gcf.id, checks.id, gcf.value
                FROM checks
                LEFT JOIN global_check_fields gcf ON gcf.name = 'transport_protocol'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id)
            (
                SELECT global_check_fields.id, checks.id
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'port'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id)
            (
                SELECT global_check_fields.id, checks.id
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'override_target'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id)
            (
                SELECT global_check_fields.id, checks.id
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'solution'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id)
            (
                SELECT global_check_fields.id, checks.id
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'solution_title'
            )"
        );
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id, value)
            (
                SELECT global_check_fields.id, checks.id, NULL
                FROM checks
                LEFT JOIN global_check_fields ON global_check_fields.name = 'poc'
            )"
        );

        // check_fields_l10n
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, check_fields.value
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'application_protocol'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, check_fields.value
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'transport_protocol'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id, \"value\")
            (
              SELECT check_fields.id, checks_l10n.language_id, check_fields.value
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'port'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id)
            (
              SELECT check_fields.id, checks_l10n.language_id
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'override_target'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id)
            (
              SELECT check_fields.id, checks_l10n.language_id
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'solution'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id)
            (
              SELECT check_fields.id, checks_l10n.language_id
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'solution_title'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );
        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id)
            (
              SELECT check_fields.id, checks_l10n.language_id
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name = 'poc'
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );

        // target_check_fields
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, target_checks.protocol
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'application_protocol'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, 'TCP'
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'transport_protocol'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, target_checks.port
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'port'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, target_checks.override_target
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'override_target'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, target_checks.solution
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'solution'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id, \"value\")
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id, target_checks.solution_title
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'solution_title'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );
        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id)
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name = 'poc'
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );

        $this->dropColumn("target_checks", "protocol");
        $this->dropColumn("target_checks", "port");
        $this->dropColumn("target_checks", "solution");
        $this->dropColumn("target_checks", "solution_title");
        $this->dropColumn("target_checks", "override_target");

        // issues
        $this->createTable(
            "issues",
            [
                "id" => "bigserial NOT NULL",
                "project_id" => "bigint NOT NULL",
                "check_id" => "bigint NOT NULL",
                "PRIMARY KEY (id)",
                "UNIQUE (project_id, check_id)",
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
            "issues_check_id_fkey",
            "issues",
            "check_id",
            "checks",
            "id",
            "CASCADE",
            "CASCADE"
        );

        // issue_evidences
        $this->createTable(
            "issue_evidences",
            [
                "id" => "bigserial NOT NULL",
                "issue_id" => "bigint NOT NULL",
                "target_check_id" => "bigint NOT NULL",
                "PRIMARY KEY (id)",
                "UNIQUE (issue_id, target_check_id)"
            ]
        );
        $this->addForeignKey(
            "issue_evidences_issue_id_fkey",
            "issue_evidences",
            "issue_id",
            "issues",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "issue_evidences_target_check_id_fkey",
            "issue_evidences",
            "target_check_id",
            "target_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addColumn("targets", "ip", "text");
        $this->addColumn("system", "host_resolve", "boolean NOT NULL DEFAULT 't'");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn("target_checks", "protocol", "varchar(1000)");
        $this->addColumn("target_checks", "port", "integer");
        $this->addColumn("target_checks", "override_target", "text");
        $this->addColumn("target_checks", "solution", "text");
        $this->addColumn("target_checks", "solution_title", "varchar(1000)");

        $this->execute(
            "UPDATE target_checks
             SET solution = target_check_fields.value
             FROM target_check_fields
             INNER JOIN check_fields ON check_fields.id = target_check_fields.check_field_id
             INNER JOIN global_check_fields ON global_check_fields.id = check_fields.global_check_field_id
             WHERE global_check_fields.name = 'solution'"
        );
        $this->execute(
            "UPDATE target_checks
             SET solution_title = target_check_fields.value
             FROM target_check_fields
             INNER JOIN check_fields ON check_fields.id = target_check_fields.check_field_id
             INNER JOIN global_check_fields ON global_check_fields.id = check_fields.global_check_field_id
             WHERE global_check_fields.name = 'solution_title'"
        );
        $this->execute(
            "UPDATE target_checks
             SET protocol = target_check_fields.value
             FROM target_check_fields
             INNER JOIN check_fields ON check_fields.id = target_check_fields.check_field_id
             INNER JOIN global_check_fields ON global_check_fields.id = check_fields.global_check_field_id
             WHERE global_check_fields.name = 'application_protocol'"
        );
        $this->execute(
            "UPDATE target_checks
             SET port = tcf.value::INTEGER
             FROM target_check_fields tcf
             INNER JOIN check_fields ON check_fields.id = tcf.check_field_id
             INNER JOIN global_check_fields ON global_check_fields.id = check_fields.global_check_field_id
             WHERE global_check_fields.name = 'port' AND tcf.value IS NOT NULL AND tcf.value != ''"
        );
        $this->execute(
            "UPDATE target_checks
             SET override_target = target_check_fields.value
             FROM target_check_fields
             INNER JOIN check_fields ON check_fields.id = target_check_fields.check_field_id
             INNER JOIN global_check_fields ON global_check_fields.id = check_fields.global_check_field_id
             WHERE global_check_fields.name = 'override_target'"
        );

        $this->execute(
            "DELETE FROM global_check_fields
             WHERE name IN
             (
              :application_protocol,
              :transport_protocol,
              :port,
              :override_target,
              :solution,
              :solution_title,
              :poc
             )", [
                "application_protocol" => GlobalCheckField::FIELD_APPLICATION_PROTOCOL,
                "transport_protocol" => GlobalCheckField::FIELD_TRANSPORT_PROTOCOL,
                "port" => GlobalCheckField::FIELD_PORT,
                "override_target" => GlobalCheckField::FIELD_OVERRIDE_TARGET,
                "solution" => GlobalCheckField::FIELD_SOLUTION,
                "solution_title" => GlobalCheckField::FIELD_SOLUTION_TITLE,
                "poc" => GlobalCheckField::FIELD_POC,
            ]
        );

        $this->dropTable("issue_evidences");
        $this->dropTable("issues");

        $this->dropColumn("system", "host_resolve");
        $this->dropColumn("targets", "ip");

		return true;
	}
}