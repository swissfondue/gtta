<?php

/**
 * Migration m150223_093912_incoming_checks
 */
class m150223_093912_incoming_checks extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->execute("DELETE FROM checks WHERE check_control_id IS NULL;");
        $this->execute("ALTER TABLE checks ALTER COLUMN check_control_id SET NOT NULL;");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->execute("ALTER TABLE checks ALTER COLUMN check_control_id DROP NOT NULL");

		return true;
	}
}