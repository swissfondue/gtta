<?php

/**
 * Migration m160819_145359_project_language
 */
class m160819_145359_project_language extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("projects", "language_id", "bigint");
        $this->addForeignKey(
            "projects_language_id_languages_id_fkey",
            "projects",
            "language_id",
            "languages",
            "id"
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("projects", "language_id");

		return true;
	}
}