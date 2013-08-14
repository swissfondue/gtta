<?php

/**
 * Migration m130814_140216_2
 */
class m130814_140216_2 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->execute("ALTER TABLE checks ALTER COLUMN check_control_id DROP NOT NULL");
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
		$this->execute("ALTER TABLE checks ALTER COLUMN check_control_id SET NOT NULL");
		return false;
	}
}