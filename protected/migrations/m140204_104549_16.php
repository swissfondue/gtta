<?php

/**
 * Migration m140204_104549_16
 */
class m140204_104549_16 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "language_id", "bigint NOT NULL DEFAULT 1");
        $this->addForeignKey(
            "system_language_id_fkey",
            "system",
            "language_id",
            "languages",
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
        $this->dropForeignKey("system_language_id_fkey", "system");
        $this->dropColumn("system", "language_id");

		return true;
	}
}