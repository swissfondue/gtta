<?php

/**
 * Migration m140105_070158_12
 */
class m140105_070158_12 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "copyright", "character varying(1000) DEFAULT 'GTTA'");
        $this->addColumn("system", "logo_type", "character varying(1000)");
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("system", "copyright");
        $this->dropColumn("system", "logo_type");
		return true;
	}
}