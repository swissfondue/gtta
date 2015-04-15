<?php

/**
 * Migration m150321_202800_target_check_relations
 */
class m150321_202800_target_check_relations extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            "targets",
            "check_source_type",
            "integer NOT NULL DEFAULT 0"
        );
        $this->execute("UPDATE targets SET check_source_type = 1 WHERE checklist_templates IS TRUE");
        $this->dropColumn("targets", "checklist_templates");

        $this->addColumn(
            "targets",
            "relation_template_id",
            "bigint DEFAULT NULL"
        );

        $this->addColumn(
            "targets",
            "relations",
            "varchar"
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn(
            "targets",
            "checklist_templates",
            "boolean NOT NULL DEFAULT 'f'"
        );
        $this->dropColumn("targets", "check_source_type");
        $this->dropColumn("targets", "relation_template_id");
        $this->dropColumn("targets", "relations");

		return true;
	}
}