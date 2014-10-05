<?php

/**
 * Migration m141003_235309_42
 */
class m141003_235309_42 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable('project_track_time_records', array(
                'id' => 'pk',
                'user_id' => 'bigserial NOT NULL',
                'project_id' => 'bigserial NOT NULL',
                'hours' => 'numeric (11,1) NOT NULL',
                'description' => 'varchar (1000)',
                'create_time' => 'timestamp DEFAULT NOW()'
            )
        );
        $this->addForeignKey(
            'project_track_time_record_user_id_fkey',
            'project_track_time_records',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'project_track_time_record_project_id_fkey',
            'project_track_time_records',
            'project_id',
            'projects',
            'id',
            'CASCADE',
            'CASCADE'
        );
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropTable('project_track_time_records');
		return true;
	}
}