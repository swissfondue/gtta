<?php

/**
 * Migration m141217_100244_replace_vulns
 */
class m141217_100244_replace_vulns extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn('target_checks', 'vuln_user_id', 'BIGINT');
        $this->addColumn('target_checks', 'vuln_deadline', 'DATE');
        $this->addColumn('target_checks', 'vuln_status', 'integer');
        $this->addColumn('target_custom_checks', 'vuln_user_id', 'BIGINT');
        $this->addColumn('target_custom_checks', 'vuln_deadline', 'DATE');
        $this->addColumn('target_custom_checks', 'vuln_status', 'integer');
        $this->execute("
            UPDATE target_checks AS tc
            SET vuln_user_id = tcv.user_id,
                vuln_deadline = tcv.deadline,
                vuln_status = tcv.status
            FROM target_check_vulns AS tcv
            WHERE tc.id = tcv.target_check_id
        ");
        $this->execute("
            UPDATE target_custom_checks as tcc
            SET vuln_user_id = tccv.user_id,
                vuln_deadline = tccv.deadline,
                vuln_status = tccv.status
            FROM target_custom_check_vulns as tccv
            WHERE tcc.id = tccv.target_custom_check_id
        ");
        $this->dropTable('target_check_vulns');
        $this->dropTable('target_custom_check_vulns');
        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->createTable(
            'target_check_vulns',
            array(
                'target_check_id'   => 'BIGINT NOT NULL',
                'user_id'           => 'BIGINT',
                'deadline'          => 'DATE',
                'status'            => 'INTEGER DEFAULT 0 NOT NULL',
            )
        );
        $this->createTable(
            'target_custom_check_vulns',
            array(
                'target_custom_check_id'    => 'BIGINT NOT NULL',
                'user_id'                   => 'BIGINT',
                'deadline'                  => 'DATE',
                'status'                    => 'INTEGER DEFAULT 0 NOT NULL',
            )
        );
        $this->execute(
            "INSERT INTO target_check_vulns (
                SELECT id, vuln_user_id, vuln_deadline, vuln_status
                FROM target_checks
                WHERE vuln_user_id IS NOT NULL
            )"
        );
        $this->execute(
            "INSERT INTO target_custom_check_vulns (
                SELECT id, vuln_user_id, vuln_deadline, vuln_status
                FROM target_custom_checks
                WHERE vuln_user_id IS NOT NULL
            )"
        );
        $this->dropColumn('target_checks', 'vuln_user_id');
        $this->dropColumn('target_checks', 'vuln_deadline');
        $this->dropColumn('target_checks', 'vuln_status');
        $this->dropColumn('target_custom_checks', 'vuln_user_id');
        $this->dropColumn('target_custom_checks', 'vuln_deadline');
        $this->dropColumn('target_custom_checks', 'vuln_status');
        $this->addForeignKey(
            "target_check_vulns_target_check_id_fkey",
            "target_check_vulns",
            "target_check_id",
            "target_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "target_custom_check_vulns_target_custom_check_id_fkey",
            "target_custom_check_vulns",
            "target_custom_check_id",
            "target_custom_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
		return true;
	}
}