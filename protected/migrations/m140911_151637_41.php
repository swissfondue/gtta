<?php

/**
 * Migration m140911_151637_41
 */
class m140911_151637_41 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable('report_template_rating_images', array(
            'report_template_id' => 'bigint NOT NULL',
            'rating_id' => 'bigint NOT NULL',
            'path' => 'varchar (1000) NOT NULL',
            'type' => 'varchar (1000) NOT NULL'
        ));

        $this->addForeignKey(
            'report_template_rating_images_report_template_id_fkey',
            'report_template_rating_images',
            'report_template_id',
            'report_templates',
            'id',
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
        $this->dropTable('report_template_rating_images');
		return true;
	}
}