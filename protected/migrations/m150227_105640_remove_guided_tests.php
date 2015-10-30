<?php

/**
 * Migration m150227_105640_remove_guided_tests
 */
class m150227_105640_remove_guided_tests extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->dropColumn(
            "projects",
            "guided_test"
        );
        $this->dropTable("project_gt_check_attachments");
        $this->dropTable("project_gt_check_inputs");
        $this->dropTable("project_gt_check_solutions");
        $this->dropTable("project_gt_check_vulns");
        $this->dropTable("project_gt_checks");
        $this->dropColumn(
            "project_planner",
            "gt_module_id"
        );
        $this->dropTable("project_gt_modules");
        $this->dropTable("project_gt_suggested_targets");

        $this->dropTable("gt_check_dependencies");
        $this->dropTable("gt_checks_l10n");
        $this->dropTable("gt_checks");
        $this->dropTable("gt_dependency_processors");
        $this->dropTable("gt_modules_l10n");
        $this->dropTable("gt_modules");
        $this->dropTable("gt_types_l10n");
        $this->dropTable("gt_types");
        $this->dropTable("gt_categories_l10n");
        $this->dropTable("gt_categories");

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        /**
         * projects table guided_test column
         */
        $this->addColumn(
            "projects",
            "guided_test",
            "boolean NOT NULL DEFAULT 'f'"
        );
        /**
         * gt_categories table
         */
        $this->createTable(
            "gt_categories",
            array(
                "id"   => "bigserial NOT NULL",
                "name" => "varchar(1000) NOT NULL",
                "PRIMARY KEY (id)"
            )
        );

        /**
         * gt_categories_l10n table
         */
        $this->createTable(
            "gt_categories_l10n",
            array(
                "gt_category_id" => "bigint NOT NULL",
                "language_id"    => "bigint NOT NULL",
                "name"           => "varchar(1000)",
                "PRIMARY KEY (gt_category_id, language_id)"
            )
        );
        $this->addForeignKey(
            "gt_categories_l10n_gt_category_id_fkey",
            "gt_categories_l10n",
            "gt_category_id",
            "gt_categories",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "gt_categories_l10n_language_id_fkey",
            "gt_categories_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * gt_types table
         */
        $this->createTable(
            "gt_types",
            array(
                "id"             => "bigserial NOT NULL",
                "gt_category_id" => "bigint NOT NULL",
                "name"           => "varchar(1000) NOT NULL",
                "PRIMARY KEY (id)"
            )
        );
        $this->addForeignKey(
            "gt_types_gt_category_id_fkey",
            "gt_types",
            "gt_category_id",
            "gt_categories",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * gt_types_l10n table
         */
        $this->createTable(
            "gt_types_l10n",
            array(
                "gt_type_id"  => "bigint NOT NULL",
                "language_id" => "bigint NOT NULL",
                "name"        => "varchar(1000)",
                "PRIMARY KEY (gt_type_id, language_id)"
            )
        );
        $this->addForeignKey(
            "gt_types_l10n_gt_type_id_fkey",
            "gt_types_l10n",
            "gt_type_id",
            "gt_types",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "gt_types_l10n_language_id_fkey",
            "gt_types_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * gt_modules table
         */
        $this->createTable(
            "gt_modules",
            array(
                "id"         => "bigserial NOT NULL",
                "gt_type_id" => "bigint NOT NULL",
                "name"       => "varchar(1000) NOT NULL",
                "PRIMARY KEY (id)"
            )
        );
        $this->addForeignKey(
            "gt_modules_gt_type_id_fkey",
            "gt_modules",
            "gt_type_id",
            "gt_types",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * gt_modules_l10n table
         */
        $this->createTable(
            "gt_modules_l10n",
            array(
                "gt_module_id"  => "bigint NOT NULL",
                "language_id" => "bigint NOT NULL",
                "name"        => "varchar(1000)",
                "PRIMARY KEY (gt_module_id, language_id)"
            )
        );
        $this->addForeignKey(
            "gt_modules_l10n_gt_module_id_fkey",
            "gt_modules_l10n",
            "gt_module_id",
            "gt_types",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "gt_modules_l10n_language_id_fkey",
            "gt_modules_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * gt_dependency_processors table
         */
        $this->createTable(
            "gt_dependency_processors",
            array(
                "id"   => "bigserial NOT NULL",
                "name" => "varchar(1000) NOT NULL",
                "PRIMARY KEY (id)"
            )
        );

        /**
         * gt_checks table
         */
        $this->createTable(
            "gt_checks",
            array(
                "id"                         => "bigserial NOT NULL",
                "gt_module_id"               => "bigint NOT NULL",
                "check_id"                   => "bigint NOT NULL",
                "description"                => "varchar(1000)",
                "target_description"         => "varchar(1000)",
                "sort_order"                 => "integer NOT NULL DEFAULT 0",
                "gt_dependency_processor_id" => "bigint",
                "PRIMARY KEY (id)"
            )
        );
        $this->addForeignKey(
            "gt_checks_check_id_fkey",
            "gt_checks",
            "check_id",
            "checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "gt_checks_gt_dependency_processor_id_fkey",
            "gt_checks",
            "gt_dependency_processor_id",
            "gt_dependency_processors",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "gt_checks_gt_module_id_fkey",
            "gt_checks",
            "gt_module_id",
            "gt_modules",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * gt_checks_l10n table
         */
        $this->createTable(
            "gt_checks_l10n",
            array(
                "gt_check_id" => "bigint NOT NULL",
                "language_id" => "bigint NOT NULL",
                "description" => "varchar(1000)",
                "target_description" => "varchar(1000)",
                "PRIMARY KEY (gt_check_id, language_id)"
            )
        );
        $this->addForeignKey(
            "gt_checks_l10n_gt_module_check_id_fkey",
            "gt_checks_l10n",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "gt_checks_l10n_language_id_fkey",
            "gt_checks_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * gt_check_dependencies table
         */
        $this->createTable(
            "gt_check_dependencies",
            array(
                "id"           => "bigserial NOT NULL",
                "gt_check_id"  => "bigint NOT NULL",
                "gt_module_id" => "bigint NOT NULL",
                "condition"    => "varchar(1000) NOT NULL",
                "PRIMARY KEY (id)"
            )
        );
        $this->addForeignKey(
            "gt_check_dependencies_gt_check_id_fkey",
            "gt_check_dependencies",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "gt_check_dependencies_gt_module_id_fkey",
            "gt_check_dependencies",
            "gt_module_id",
            "gt_modules",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_planner table new column
         */
        $this->addColumn(
            "project_planner",
            "gt_module_id",
            "bigint"
        );
        $this->addForeignKey(
            "project_planner_gt_module_id_fkey",
            "project_planner",
            "gt_module_id",
            "gt_modules",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_gt_checks table
         */
        $this->createTable(
            "project_gt_checks",
            array(
                "project_id"     => "bigint NOT NULL",
                "gt_check_id"    => "bigint NOT NULL",
                "user_id"        => "bigint NOT NULL",
                "language_id"    => "bigint NOT NULL",
                "target"         => "varchar(1000)",
                "port"           => "integer",
                "protocol"       => "varchar(1000)",
                "target_file"    => "varchar(1000)",
                "result_file"    => "varchar(1000)",
                "result"         => "varchar(1000)",
                "table_result"   => "varchar(1000)",
                "status"         => "check_status NOT NULL DEFAULT 'open'::check_status",
                "rating"         => "integer NOT NULL DEFAULT 0",
                "solution"       => "varchar(1000)",
                "solution_title" => "varchar(1000) DEFAULT NULL::varchar",
                "PRIMARY KEY (project_id, gt_check_id)"
            )
        );
        $this->addForeignKey(
            "project_gt_checks_gt_check_id_fkey",
            "project_gt_checks",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_checks_language_id_fkey",
            "project_gt_checks",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_checks_project_id_fkey",
            "project_gt_checks",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_checks_user_id_fkey",
            "project_gt_checks",
            "user_id",
            "users",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_gt_modules table
         */
        $this->createTable(
            "project_gt_modules",
            array(
                "project_id"   => "bigint NOT NULL",
                "gt_module_id" => "bigint NOT NULL",
                "sort_order"   => "integer NOT NULL",
                "PRIMARY KEY (project_id, gt_module_id)"
            )
        );
        $this->addForeignKey(
            "project_gt_modules_gt_module_id_fkey",
            "project_gt_modules",
            "gt_module_id",
            "gt_modules",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_modules_project_id_fkey",
            "project_gt_modules",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_gt_suggested_targets table
         */
        $this->createTable(
            "project_gt_suggested_targets",
            array(
                "id"           => "bigserial NOT NULL",
                "project_id"   => "bigint NOT NULL",
                "gt_module_id" => "bigint NOT NULL",
                "target"       => "varchar(1000) NOT NULL",
                "gt_check_id"  => "bigint NOT NULL",
                "approved"     => "boolean NOT NULL DEFAULT 'f'",
                "PRIMARY KEY (id)"
            )
        );
        $this->addForeignKey(
            "project_gt_suggested_targets_gt_check_id_fkey",
            "project_gt_suggested_targets",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_suggested_targets_gt_module_id_fkey",
            "project_gt_suggested_targets",
            "gt_module_id",
            "gt_modules",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_suggested_targets_project_id_fkey",
            "project_gt_suggested_targets",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_gt_check_attachments_table
         */
        $this->createTable(
            "project_gt_check_attachments",
            array(
                "project_id"  => "bigint NOT NULL",
                "gt_check_id" => "bigint NOT NULL",
                "name"        => "varchar(1000) NOT NULL",
                "type"        => "varchar(1000) NOT NULL",
                "path"        => "varchar(1000) NOT NULL",
                "size"        => "bigint NOT NULL DEFAULT 0",
                "title"       => "varchar(1000)",
                "PRIMARY KEY (path)"
            )
        );
        $this->addForeignKey(
            "project_gt_check_attachments_gt_check_id_fkey",
            "project_gt_check_attachments",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_attachments_project_id_fkey",
            "project_gt_check_attachments",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_attachments_project_id_gt_check_id_fkey",
            "project_gt_check_attachments",
            "project_id, gt_check_id",
            "project_gt_checks",
            "project_id, gt_check_id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_gt_check_inputs table
         */
        $this->createTable(
            "project_gt_check_inputs",
            array(
                "project_id"     => "bigint NOT NULL",
                "gt_check_id"    => "bigint NOT NULL",
                "check_input_id" => "bigint NOT NULL",
                "value"          => "varchar(1000)",
                "file"           => "varchar(1000)",
                "PRIMARY KEY (project_id, gt_check_id, check_input_id)"
            )
        );
        $this->addForeignKey(
            "project_gt_check_inputs_check_input_id_fkey",
            "project_gt_check_inputs",
            "check_input_id",
            "check_inputs",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_inputs_gt_check_id_fkey",
            "project_gt_checks",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_inputs_project_id_fkey",
            "project_gt_checks",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_gt_check_solutions table
         */
        $this->createTable(
            "project_gt_check_solutions",
            array(
                "project_id"        => "bigint NOT NULL",
                "gt_check_id"       => "bigint NOT NULL",
                "check_solution_id" => "bigint NOT NULL",
                "PRIMARY KEY (project_id, gt_check_id, check_solution_id)"
            )
        );
        $this->addForeignKey(
            "project_gt_check_solutions_check_solution_id_fkey",
            "project_gt_check_solutions",
            "check_solution_id",
            "check_solutions",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_solutions_gt_check_id_fkey",
            "project_gt_check_solutions",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_solutions_project_id_fkey",
            "project_gt_check_solutions",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_solutions_project_id_gt_check_id_fkey",
            "project_gt_check_solutions",
            "project_id, gt_check_id",
            "project_gt_checks",
            "project_id, gt_check_id",
            "CASCADE",
            "CASCADE"
        );

        /**
         * project_gt_check_vulns table
         */
        $this->createTable(
            "project_gt_check_vulns",
            array(
                "project_id" => "bigint NOT NULL",
                "gt_check_id" => "bigint NOT NULL",
                "user_id"     => "bigint",
                "deadline"    => "date",
                "status"      => "vuln_status NOT NULL DEFAULT 'open'::vuln_status",
                "PRIMARY KEY (project_id, gt_check_id)"
            )
        );
        $this->addForeignKey(
            "project_gt_check_vulns_gt_check_id_fkey",
            "project_gt_check_vulns",
            "gt_check_id",
            "gt_checks",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_vulns_project_id_fkey",
            "project_gt_check_vulns",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_vulns_project_id_gt_check_id_fkey",
            "project_gt_check_vulns",
            "project_id",
            "projects",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "project_gt_check_vulns_user_id_fkey",
            "project_gt_check_vulns",
            "user_id",
            "users",
            "id",
            "CASCADE",
            "CASCADE"
        );

        return true;
	}
}