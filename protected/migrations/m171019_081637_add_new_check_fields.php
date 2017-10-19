<?php

/**
 * Migration m171019_081637_add_new_check_fields
 */
class m171019_081637_add_new_check_fields extends CDbMigration {
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
                  where global_check_fields.name IN ('technical_solution', 
                  'management_solution', 'technical_result', 'management_result')
             )"
        );
        $this->execute("UPDATE global_check_fields SET sort_order = 7 WHERE global_check_fields.name = 'application_protocol'");
        $this->execute("UPDATE global_check_fields SET sort_order = 8 WHERE global_check_fields.name = 'transport_protocol'");
        $this->execute("UPDATE global_check_fields SET sort_order = 9 WHERE global_check_fields.name = 'port'");
        $this->execute("UPDATE global_check_fields SET sort_order = 10 WHERE global_check_fields.name = 'override_target'");
        $this->execute("UPDATE global_check_fields SET sort_order = 11 WHERE global_check_fields.name = 'solution_title'");
        $this->execute("UPDATE global_check_fields SET sort_order = 12 WHERE global_check_fields.name = 'solution'");
        $this->execute("UPDATE global_check_fields SET sort_order = 15 WHERE global_check_fields.name = 'poc'");
        return true;
    }

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->execute(
            "DELETE FROM global_check_fields 
              WHERE global_check_fields.name IN ('technical_solution', 
                  'management_solution', 'technical_result', 'management_result')"
        );

        return true;
    }
}


