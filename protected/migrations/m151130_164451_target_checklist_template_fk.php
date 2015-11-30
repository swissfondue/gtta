<?php

/**
 * Migration m151130_164451_target_checklist_template_fk
 */
class m151130_164451_target_checklist_template_fk extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addForeignKey(
            "target_checklist_templates_target_id_fkey",
            "target_checklist_templates",
            "target_id",
            "targets",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_checklist_templates_checklist_template_id_fkey",
            "target_checklist_templates",
            "checklist_template_id",
            "checklist_templates",
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
        $this->dropForeignKey("target_checklist_templates_target_id_fkey", "target_checklist_templates");
        $this->dropForeignKey("target_checklist_templates_checklist_template_id_fkey", "target_checklist_templates");

		return true;
	}
}