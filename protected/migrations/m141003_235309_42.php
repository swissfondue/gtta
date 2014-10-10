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
        $this->createTable('project_time', array(
                'id' => 'pk',
                'user_id' => 'bigint NOT NULL',
                'project_id' => 'bigint NOT NULL',
                'hours' => 'numeric (11,1) NOT NULL',
                'description' => 'varchar (1000)',
                'create_time' => 'timestamp DEFAULT NOW()'
            )
        );
        $this->addForeignKey(
            'project_time_user_id_fkey',
            'project_time',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'project_time_project_id_fkey',
            'project_time',
            'project_id',
            'projects',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->dropColumn('project_users', 'hours_spent');

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropTable('project_time');
		return true;
	}
}