<?php

/**
 * Migration m141113_153020_languages_user_default_column_add
 */
class m141113_153020_languages_user_default_column_add extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            'languages',
            'user_default',
            'BOOLEAN NOT NULL DEFAULT FALSE'
        );

        // set English as default
        $this->update("languages", array("user_default" => true), "id = :id", array("id" => 1));

        // Only one row with user_default = TRUE in `languages` table
        $this->execute('CREATE UNIQUE INDEX user_default_unique ON languages(user_default) WHERE user_default');
        $this->dropColumn('system', 'language_id');

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropIndex('user_default_unique', 'languages');
        $this->dropColumn('languages', 'user_default');
        $this->addColumn('system', 'language_id', 'bigint NOT NULL DEFAULT 1');

        return true;
	}
}