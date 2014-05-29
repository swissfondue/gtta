<?php

/**
 * Migration m140526_155244_32
 */
class m140526_155244_32 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     * @throws Exception
     */
    public function safeUp() {
        $this->createTable("target_checks_new", array(
            "id" => "bigserial NOT NULL",
            "target_id" => "bigint NOT NULL",
            "check_id" => "bigint NOT NULL",
            "result" => "character varying",
            "target_file" => "character varying(1000)",
            "started" => "timestamp without time zone",
            "pid" => "bigint",
            "status" => "integer NOT NULL DEFAULT 0",
            "result_file" => "character varying(1000)",
            "language_id" => "bigint NOT NULL",
            "protocol" => "character varying(1000)",
            "port" => "integer",
            "override_target" => "character varying(1000)",
            "user_id" => "bigint NOT NULL",
            "table_result" => "character varying",
            "rating" => "integer NOT NULL DEFAULT 0",
            "solution" => "character varying",
            "solution_title" => "character varying(1000)",
            "PRIMARY KEY (id)",
        ));

        $this->createTable("target_check_attachments_new", array(
            "target_check_id" => "bigint NOT NULL",
            "name" => "character varying(1000)",
            "type" => "character varying(1000)",
            "path" => "character varying(1000)",
            "size" => "bigint NOT NULL DEFAULT 0",
            "PRIMARY KEY (path)",
        ));

        $this->createTable("target_check_inputs_new", array(
            "target_check_id" => "bigint NOT NULL",
            "check_input_id" => "bigint NOT NULL",
            "value" => "character varying",
            "file" => "character varying(1000)",
            "PRIMARY KEY (target_check_id, check_input_id)",
        ));

        $this->createTable("target_check_solutions_new", array(
            "target_check_id" => "bigint NOT NULL",
            "check_solution_id" => "bigint NOT NULL",
            "PRIMARY KEY (target_check_id, check_solution_id)",
        ));

        $this->createTable("target_check_vulns_new", array(
            "target_check_id" => "bigint NOT NULL",
            "user_id" => "bigint",
            "deadline" => "date",
            "status" => "integer NOT NULL DEFAULT 0",
            "PRIMARY KEY (target_check_id)",
        ));

        // insert data
        $checks = $this->getDbConnection()->createCommand("SELECT * FROM target_checks")->query();

        foreach ($checks as $check) {
            $status = $check["status"];

            switch ($status) {
                case "open":
                    $status = 0;
                    break;

                case "in_progress":
                    $status = 10;
                    break;

                case "stop":
                    $status = 50;
                    break;

                case "finished":
                    $status = 100;
                    break;

                default:
                    echo "Unknown status: " . $status;
                    return false;
            }

            $this->insert("target_checks_new", array(
                "target_id" => $check["target_id"],
                "check_id" => $check["check_id"],
                "result" => $check["result"],
                "target_file" => $check["target_file"],
                "started" => $check["started"],
                "pid" => $check["pid"],
                "status" => $status,
                "result_file" => $check["result_file"],
                "language_id" => $check["language_id"],
                "protocol" => $check["protocol"],
                "port" => $check["port"],
                "override_target" => $check["override_target"],
                "user_id" => $check["user_id"],
                "table_result" => $check["table_result"],
                "rating" => $check["rating"],
                "solution" => $check["solution"],
                "solution_title" => $check["solution_title"],
            ));
        }

        // cache target check data
        $checks = $this->getDbConnection()->createCommand("SELECT * FROM target_checks_new")->query();
        $cache = array();

        foreach ($checks as $check) {
            $cache[$check["target_id"] . "-" . $check["check_id"]] = $check["id"];
        }

        // attachments
        $attachments = $this->getDbConnection()->createCommand("SELECT * FROM target_check_attachments")->query();

        foreach ($attachments as $attachment) {
            $this->insert("target_check_attachments_new", array(
                "target_check_id" => $cache[$attachment["target_id"] . "-" . $attachment["check_id"]],
                "name" => $attachment["name"],
                "type" => $attachment["type"],
                "path" => $attachment["path"],
                "size" => $attachment["size"],
            ));
        }
        
        // inputs
        $inputs = $this->getDbConnection()->createCommand("SELECT * FROM target_check_inputs")->query();

        foreach ($inputs as $input) {
            $this->insert("target_check_inputs_new", array(
                "target_check_id" => $cache[$input["target_id"] . "-" . $input["check_id"]],
                "check_input_id" => $input["check_input_id"],
                "value" => $input["value"],
                "file" => $input["file"],
            ));
        }
        
        // solutions
        $solutions = $this->getDbConnection()->createCommand("SELECT * FROM target_check_solutions")->query();

        foreach ($solutions as $solution) {
            $this->insert("target_check_solutions_new", array(
                "target_check_id" => $cache[$solution["target_id"] . "-" . $solution["check_id"]],
                "check_solution_id" => $solution["check_solution_id"],
            ));
        }
        
        // vulns
        $vulns = $this->getDbConnection()->createCommand("SELECT * FROM target_check_vulns")->query();

        foreach ($vulns as $vuln) {
            $status = $vuln["status"];

            switch ($status) {
                case "open":
                    $status = 0;
                    break;

                case "resolved":
                    $status = 100;
                    break;

                default:
                    echo "Unknown status: " . $status;
                    return false;
            }

            $this->insert("target_check_vulns_new", array(
                "target_check_id" => $cache[$vuln["target_id"] . "-" . $vuln["check_id"]],
                "user_id" => $vuln["user_id"],
                "deadline" => $vuln["deadline"],
                "status" => $status,
            ));
        }

        // drop old tables
        $this->dropTable("target_check_attachments");
        $this->dropTable("target_check_inputs");
        $this->dropTable("target_check_solutions");
        $this->dropTable("target_check_vulns");
        $this->dropTable("target_checks");

        // rename new tables
        $this->renameTable("target_check_attachments_new", "target_check_attachments");
        $this->renameTable("target_check_inputs_new", "target_check_inputs");
        $this->renameTable("target_check_solutions_new", "target_check_solutions");
        $this->renameTable("target_check_vulns_new", "target_check_vulns");
        $this->renameTable("target_checks_new", "target_checks");

        // create foreign keys
        $this->addForeignKey(
            "target_checks_target_id_fkey",
            "target_checks",
            "target_id",
            "targets",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_checks_check_id_fkey",
            "target_checks",
            "check_id",
            "checks",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_checks_language_id_fkey",
            "target_checks",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_check_attachments_target_check_id_fkey",
            "target_check_attachments",
            "target_check_id",
            "target_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        
        $this->addForeignKey(
            "target_check_inputs_target_check_id_fkey",
            "target_check_inputs",
            "target_check_id",
            "target_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_check_inputs_check_input_id_fkey",
            "target_check_inputs",
            "check_input_id",
            "check_inputs",
            "id",
            "CASCADE",
            "CASCADE"
        );
        
        $this->addForeignKey(
            "target_check_solutions_target_check_id_fkey",
            "target_check_solutions",
            "target_check_id",
            "target_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "target_check_solutions_check_solution_id_fkey",
            "target_check_solutions",
            "check_solution_id",
            "check_solutions",
            "id",
            "CASCADE",
            "CASCADE"
        );
        
        $this->addForeignKey(
            "target_check_vulns_target_check_id_fkey",
            "target_check_vulns",
            "target_check_id",
            "target_checks",
            "id",
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
        $this->createTable("target_checks_old", array(
            "target_id" => "bigint NOT NULL",
            "check_id" => "bigint NOT NULL",
            "result" => "character varying",
            "target_file" => "character varying(1000)",
            "started" => "timestamp without time zone",
            "pid" => "bigint",
            "status" => "check_status NOT NULL DEFAULT 'open'::check_status",
            "result_file" => "character varying(1000)",
            "language_id" => "bigint NOT NULL",
            "protocol" => "character varying(1000)",
            "port" => "integer",
            "override_target" => "character varying(1000)",
            "user_id" => "bigint NOT NULL",
            "table_result" => "character varying",
            "rating" => "integer NOT NULL DEFAULT 0",
            "solution" => "character varying",
            "solution_title" => "character varying(1000)",
            "PRIMARY KEY (target_id, check_id)",
        ));

        $this->createTable("target_check_attachments_old", array(
            "target_id" => "bigint NOT NULL",
            "check_id" => "bigint NOT NULL",
            "name" => "character varying(1000)",
            "type" => "character varying(1000)",
            "path" => "character varying(1000)",
            "size" => "bigint NOT NULL DEFAULT 0",
            "PRIMARY KEY (path)",
        ));

        $this->createTable("target_check_inputs_old", array(
            "target_id" => "bigint NOT NULL",
            "check_id" => "bigint NOT NULL",
            "check_input_id" => "bigint NOT NULL",
            "value" => "character varying",
            "file" => "character varying(1000)",
            "PRIMARY KEY (target_id, check_input_id)",
        ));

        $this->createTable("target_check_solutions_old", array(
            "target_id" => "bigint NOT NULL",
            "check_id" => "bigint NOT NULL",
            "check_solution_id" => "bigint NOT NULL",
            "PRIMARY KEY (target_id, check_solution_id)",
        ));

        $this->createTable("target_check_vulns_old", array(
            "target_id" => "bigint NOT NULL",
            "check_id" => "bigint NOT NULL",
            "user_id" => "bigint",
            "deadline" => "date",
            "status" => "vuln_status NOT NULL DEFAULT 'open'::vuln_status",
            "PRIMARY KEY (target_id, check_id)",
        ));

        // insert data
        $checks = $this->getDbConnection()->createCommand("SELECT * FROM target_checks")->query();
        $cache = array();

        foreach ($checks as $check) {
            $status = $check["status"];

            switch ($status) {
                case 0:
                    $status = "open";
                    break;

                case 10:
                    $status = "in_progress";
                    break;

                case 50:
                    $status = "stop";
                    break;

                case 100:
                    $status = "finished";
                    break;

                default:
                    echo "Unknown status: " . $status;
                    return false;
            }

            $this->insert("target_checks_old", array(
                "target_id" => $check["target_id"],
                "check_id" => $check["check_id"],
                "result" => $check["result"],
                "target_file" => $check["target_file"],
                "started" => $check["started"],
                "pid" => $check["pid"],
                "status" => $status,
                "result_file" => $check["result_file"],
                "language_id" => $check["language_id"],
                "protocol" => $check["protocol"],
                "port" => $check["port"],
                "override_target" => $check["override_target"],
                "user_id" => $check["user_id"],
                "table_result" => $check["table_result"],
                "rating" => $check["rating"],
                "solution" => $check["solution"],
                "solution_title" => $check["solution_title"],
            ));

            $cache[$check["id"]] = array(
                "target_id" => $check["target_id"],
                "check_id" => $check["check_id"],
            );
        }

        // attachments
        $attachments = $this->getDbConnection()->createCommand("SELECT * FROM target_check_attachments")->query();

        foreach ($attachments as $attachment) {
            $this->insert("target_check_attachments_old", array(
                "target_id" => $cache[$attachment["target_check_id"]]["target_id"],
                "check_id" => $cache[$attachment["target_check_id"]]["check_id"],
                "name" => $attachment["name"],
                "type" => $attachment["type"],
                "path" => $attachment["path"],
                "size" => $attachment["size"],
            ));
        }

        // inputs
        $inputs = $this->getDbConnection()->createCommand("SELECT * FROM target_check_inputs")->query();

        foreach ($inputs as $input) {
            $this->insert("target_check_inputs_old", array(
                "target_id" => $cache[$input["target_check_id"]]["target_id"],
                "check_id" => $cache[$input["target_check_id"]]["check_id"],
                "check_input_id" => $input["check_input_id"],
                "value" => $input["value"],
                "file" => $input["file"],
            ));
        }

        // solutions
        $solutions = $this->getDbConnection()->createCommand("SELECT * FROM target_check_solutions")->query();

        foreach ($solutions as $solution) {
            $this->insert("target_check_solutions_old", array(
                "target_id" => $cache[$solution["target_check_id"]]["target_id"],
                "check_id" => $cache[$solution["target_check_id"]]["check_id"],
                "check_solution_id" => $solution["check_solution_id"],
            ));
        }

        // vulns
        $vulns = $this->getDbConnection()->createCommand("SELECT * FROM target_check_vulns")->query();

        foreach ($vulns as $vuln) {
            $status = $vuln["status"];

            switch ($status) {
                case 0:
                    $status = "open";
                    break;

                case 100:
                    $status = "resolved";
                    break;

                default:
                    echo "Unknown status: " . $status;
                    return false;
            }

            $this->insert("target_check_vulns_old", array(
                "target_id" => $cache[$vuln["target_check_id"]]["target_id"],
                "check_id" => $cache[$vuln["target_check_id"]]["check_id"],
                "user_id" => $vuln["user_id"],
                "deadline" => $vuln["deadline"],
                "status" => $status,
            ));
        }

        // drop old tables
        $this->dropTable("target_check_attachments");
        $this->dropTable("target_check_inputs");
        $this->dropTable("target_check_solutions");
        $this->dropTable("target_check_vulns");
        $this->dropTable("target_checks");

        // rename new tables
        $this->renameTable("target_check_attachments_old", "target_check_attachments");
        $this->renameTable("target_check_inputs_old", "target_check_inputs");
        $this->renameTable("target_check_solutions_old", "target_check_solutions");
        $this->renameTable("target_check_vulns_old", "target_check_vulns");
        $this->renameTable("target_checks_old", "target_checks");

        return true;
	}
}