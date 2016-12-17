<?php

/**
 * Migration m161214_154906_fields_external_id
 */
class m161214_154906_fields_external_id extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("global_check_fields", "external_id", "bigint");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("global_check_fields", "external_id");

		return true;
	}
}