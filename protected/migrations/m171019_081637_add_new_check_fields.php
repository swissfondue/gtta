<?php

/**
 * Migration m171019_081637_add_new_check_fields
 */
class m171019_081637_add_new_check_fields extends CDbMigration {
    const NEW_FIELDS_SQL = "('technical_solution', 'management_solution', 'technical_result', 'management_result')";

    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->execute(
            "INSERT INTO global_check_fields (type, name, title, sort_order, hidden)
             VALUES
             (:textarea_type, 'technical_solution', 'Solution (technical summary)', 13, true),
             (:textarea_type, 'management_solution', 'Solution (management summary)', 14, true),
             (:textarea_type, 'technical_result', 'Result (technical summary)', 5, true),
             (:textarea_type, 'management_result', 'Result (management summary)', 6, true)",
            [
                "textarea_type" => GlobalCheckField::TYPE_TEXTAREA
            ]
        );

        $this->execute(
            "INSERT INTO global_check_fields_l10n (global_check_field_id, language_id, title)
             (
                  SELECT global_check_fields.id, languages.id, global_check_fields.title
                  FROM global_check_fields LEFT JOIN languages ON languages.code = 'en' 
                  where global_check_fields.name IN " . self::NEW_FIELDS_SQL . "
             )"
        );
        $this->execute("UPDATE global_check_fields SET sort_order = 7 WHERE global_check_fields.name = 'application_protocol'");
        $this->execute("UPDATE global_check_fields SET sort_order = 8 WHERE global_check_fields.name = 'transport_protocol'");
        $this->execute("UPDATE global_check_fields SET sort_order = 9 WHERE global_check_fields.name = 'port'");
        $this->execute("UPDATE global_check_fields SET sort_order = 10 WHERE global_check_fields.name = 'override_target'");
        $this->execute("UPDATE global_check_fields SET sort_order = 11 WHERE global_check_fields.name = 'solution_title'");
        $this->execute("UPDATE global_check_fields SET sort_order = 12 WHERE global_check_fields.name = 'solution'");
        $this->execute("UPDATE global_check_fields SET sort_order = 15 WHERE global_check_fields.name = 'poc'");

        // update check_fields and target_check_fields
        $this->execute(
            "INSERT INTO check_fields (global_check_field_id, check_id)
            (
                SELECT gcf.id, checks.id
                FROM checks
                LEFT JOIN global_check_fields gcf ON gcf.name IN " . self::NEW_FIELDS_SQL . "
            )"
        );

        $this->execute(
            "INSERT INTO check_fields_l10n (check_field_id, language_id)
            (
              SELECT check_fields.id, checks_l10n.language_id
              FROM checks_l10n
              LEFT JOIN checks ON checks_l10n.check_id = checks.id
              LEFT JOIN global_check_fields ON global_check_fields.name IN " . self::NEW_FIELDS_SQL . "
              LEFT JOIN check_fields ON check_fields.check_id = checks.id AND check_fields.global_check_field_id = global_check_fields.id
            )"
        );

        $this->execute(
            "INSERT INTO target_check_fields (target_check_id, check_field_id)
             (
               SELECT target_checks.id as target_check_id, check_fields.id as check_field_id
               FROM target_checks
               LEFT JOIN checks ON checks.id = target_checks.check_id
               LEFT JOIN global_check_fields ON global_check_fields.name IN " . self::NEW_FIELDS_SQL . "
               LEFT JOIN check_fields ON check_fields.check_id = target_checks.check_id AND check_fields.global_check_field_id = global_check_fields.id
             )"
        );

        return true;
    }

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->execute("DELETE FROM global_check_fields WHERE global_check_fields.name IN " . self::NEW_FIELDS_SQL);
        return true;
    }
}


