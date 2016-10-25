<?php

/**
 * Migration m161013_200257_nessus_mapping
 */
class m161013_200257_nessus_mapping extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable("nessus_mappings", [
                "id" => "bigserial",
                "name" => "varchar(1000)",
                "created_at" => "timestamp WITHOUT TIME ZONE NOT NULL DEFAULT NOW()",
                "PRIMARY KEY (id)"
            ]
        );
        $this->createTable("nessus_mapping_vulns", [
                "id" => "bigserial",
                "nessus_mapping_id" => "bigint NOT NULL",
                "nessus_plugin_id" => "bigint NOT NULL",
                "nessus_plugin_name" => "varchar NOT NULL",
                "nessus_rating" => "varchar NOT NULL",
                "nessus_host" => "string NOT NULL",
                "check_id" => "bigint",
                "check_result_id" => "bigint",
                "check_solution_id" => "bigint",
                "rating" => "integer",
                "active" => "boolean NOT NULL DEFAULT 't'",
                "PRIMARY KEY (id)",
                "UNIQUE(check_id, nessus_plugin_id)"
            ]
        );
        $this->addForeignKey(
            "nessus_mapping_vulns_nessus_mapping_id_fkey",
            "nessus_mapping_vulns",
            "nessus_mapping_id",
            "nessus_mappings",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "nessus_mapping_vulns_check_id_fkey",
            "nessus_mapping_vulns",
            "check_id",
            "checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "nessus_mapping_vulns_check_result_id_fkey",
            "nessus_mapping_vulns",
            "check_result_id",
            "check_results",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "nessus_mapping_vulns_check_solution_id_fkey",
            "nessus_mapping_vulns",
            "check_solution_id",
            "check_solutions",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->createTable("nessus_mappings_l10n", [
            "nessus_mapping_id" => "bigint NOT NULL",
            "language_id" => "bigint NOT NULL",
            "name" => "varchar(1000)",
            "PRIMARY KEY(nessus_mapping_id, language_id)"
        ]);
        $this->addForeignKey(
            "nessus_mappings_l10n_nessus_mapping_id_fkey",
            "nessus_mappings_l10n",
            "nessus_mapping_id",
            "nessus_mappings",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->execute("insert into nessus_mappings (id, name) values (1, 'kokoko petuh kukarek');");
        $this->execute(
            "insert into nessus_mapping_vulns 
             (nessus_mapping_id, nessus_plugin_id, nessus_host, nessus_plugin_name, nessus_rating, check_id, rating, check_result_id, check_solution_id)
             values 
             (1, 1, '127.0.0.1', 'Plugin God','None', 1, 100, 1, 1);"
        );
        $this->execute(
            "insert into nessus_mapping_vulns 
             (nessus_mapping_id, nessus_plugin_id, nessus_host, nessus_plugin_name, nessus_rating, check_id, rating, check_result_id, check_solution_id)
             values 
             (1, 2, '10.10.10.1', 'Plugin Dick','Med', 2, 200, 2, 2);"
        );
        $this->execute(
            "insert into nessus_mapping_vulns 
             (nessus_mapping_id, nessus_plugin_id, nessus_host, nessus_plugin_name, nessus_rating, check_id, rating, check_result_id, check_solution_id)
             values 
             (1, 3, '192.168.0.1', 'Plugin Cum','High', 3, 500, 3, 3);"
        );
        $this->execute(
            "insert into nessus_mapping_vulns 
             (nessus_mapping_id, nessus_plugin_id, nessus_host, nessus_plugin_name, nessus_rating, check_id, rating, check_result_id, check_solution_id)
             values 
             (1, 4, '192.168.0.1', 'Plugin Penis','Low', 4, 100, 4, 4);"
        );

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropTable("nessus_mapping_vulns");
        $this->dropTable("nessus_mappings_l10n");
        $this->dropTable("nessus_mappings");

		return true;
	}
}