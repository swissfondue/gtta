<?php

/**
 * Migration m150411_083537_remove_advanced
 */
class m150411_083537_remove_advanced extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->dropColumn(
            "target_check_categories",
            "advanced"
        );

        $this->dropColumn(
            "checks",
            "advanced"
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->addColumn(
            "target_check_categories",
            "advanced",
            "boolean NOT NULL DEFAULT 'f'"
        );

        $this->addColumn(
            "checks",
            "advanced",
            "boolean NOT NULL DEFAULT 'f'"
        );

		return true;
	}
}