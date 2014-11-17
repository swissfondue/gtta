<?php

/**
 * Migration m141031_150425_add_target_check_attachments_title_column
 */
class m141031_150425_add_target_check_attachments_title_column extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn(
            'target_check_attachments',
            'title',
            'VARCHAR(1000)'
        );
        $this->addColumn(
            'target_custom_check_attachments',
            'title',
            'VARCHAR(1000)'
        );
        $this->addColumn(
            'project_gt_check_attachments',
            'title',
            'VARCHAR(1000)'
        );

        $this->execute("UPDATE target_check_attachments SET title = name");
        $this->execute("UPDATE target_custom_check_attachments SET title = name");
        $this->execute("UPDATE project_gt_check_attachments SET title = name");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropColumn('target_check_attachments', 'title');
        $this->dropColumn('target_custom_check_attachments', 'title');
        $this->dropColumn('project_gt_check_attachments', 'title');

		return true;
	}
}