<?php

/**
 * Migration m141022_175351_drop_checks_l10n_reference_column
 */
class m141022_175351_drop_checks_l10n_reference_column extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->dropColumn('checks_l10n', 'reference');
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
		return true;
	}
}