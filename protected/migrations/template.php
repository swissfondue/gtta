<?php

/**
 * Migration {ClassName}
 */
class {ClassName} extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
		echo "{ClassName} does not support migration down.\\n";
		return false;
	}
}