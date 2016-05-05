<?php

/**
 * Migration m150923_130001_git_support
 */
class m150923_130001_git_support extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("system", "git_url", "text");
        $this->addColumn("system", "git_proto", "text NOT NULL DEFAULT 0");
        $this->addColumn("system", "git_username", "text");
        $this->addColumn("system", "git_password", "text");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn("system", "git_url");
        $this->dropColumn("system", "git_proto");
        $this->dropColumn("system", "git_username");
        $this->dropColumn("system", "git_password");

        return true;
	}
}