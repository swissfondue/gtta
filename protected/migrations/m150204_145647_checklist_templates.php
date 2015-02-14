<?php

/**
 * Migration m150204_145647_checklist_templates
 */
class m150204_145647_checklist_templates extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        /**
         * checklist_template_categories table
         */
        $this->createTable(
            "checklist_template_categories",
            array(
                "id" => "bigserial NOT NULL",
                "name" => "varchar (100) NOT NULL",
                "PRIMARY KEY (id)"
            )
        );

        /**
         * checklist_template_categories_l10n
         */
        $this->createTable(
            "checklist_template_categories_l10n",
            array(
                "checklist_template_category_id" => "bigint NOT NULL",
                "language_id" => "bigint NOT NULL",
                "name" => "varchar (1000)",
                "PRIMARY KEY (checklist_template_category_id, language_id)"
            )
        );

        /**
         * checklist_templates table
         */
        $this->createTable(
            "checklist_templates",
            array(
                "id" => "bigserial NOT NULL",
                "checklist_template_category_id" => "bigint NOT NULL",
                "name" => "varchar (1000) NOT NULL",
                "description" => "varchar (1000)",
                "PRIMARY KEY (id)"
            )
        );

        /**
         * checklist_templates_l10n table
         */
        $this->createTable(
            "checklist_templates_l10n",
            array(
                "checklist_template_id" => "bigint NOT NULL",
                "language_id" => "bigint NOT NULL",
                "name" => "varchar (1000)",
                "description" => "varchar (1000)",
                "PRIMARY KEY (checklist_template_id, language_id)"
            )
        );

        /**
         * target_check_checklist_templates table
         */
        $this->createTable(
            "target_check_checklist_templates",
            array(
                "target_id" => "bigint NOT NULL",
                "checklist_template_id" => "bigint NOT NULL",
                "PRIMARY KEY (target_id, checklist_template_id)",
            )
        );

        /**
         * targets table (flag if checklist templates choosen)
         */
        $this->addColumn(
            "targets",
            "checklist_templates",
            "boolean NOT NULL DEFAULT 'f'"
        );

        /**
         * target_check_categories table
         */
        $this->addColumn(
            "target_check_categories",
            "checklist_template",
            "boolean NOT NULL DEFAULT 'f'"
        );

        $this->addColumn(
            "target_check_categories",
            "template_count",
            "bigint DEFAULT 1"
        );

        /**
         * checklist_template_checks table
         */
        $this->createTable(
            "checklist_template_checks",
            array(
                "checklist_template_id" => "bigint NOT NULL",
                "check_id" => "bigint NOT NULL",
                "PRIMARY KEY (checklist_template_id, check_id)"
            )
        );

        $this->addForeignKey(
            "checklist_template_categories_l10n_language_id_fkey",
            "checklist_template_categories_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "checklist_template_categories_l10n_checklist_template_category_id_fkey",
            "checklist_template_categories_l10n",
            "checklist_template_category_id",
            "checklist_template_categories",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "checklist_templates_check_template_category_id_fkey",
            "checklist_templates",
            "checklist_template_category_id",
            "checklist_template_categories",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "checklist_templates_l10n_checklist_template_id_fkey",
            "checklist_templates_l10n",
            "checklist_template_id",
            "checklist_templates",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "checklist_templates_l10n_language_id_fkey",
            "checklist_templates_l10n",
            "language_id",
            "languages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "checklist_template_checks_checklist_template_id_fkey",
            "checklist_template_checks",
            "checklist_template_id",
            "checklist_templates",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $this->addForeignKey(
            "checklist_template_checks_check_id_fkey",
            "checklist_template_checks",
            "check_id",
            "checks",
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
        $this->dropColumn("target_check_categories", "checklist_template");
        $this->dropColumn("targets", "checklist_templates");
        $this->dropColumn("target_check_categories", "template_count");
        $this->dropTable("target_check_checklist_templates");
        $this->dropTable("checklist_template_checks");
        $this->dropTable("checklist_templates_l10n");
        $this->dropTable("checklist_templates");
        $this->dropTable("checklist_template_categories_l10n");
        $this->dropTable("checklist_template_categories");

        return true;
    }
}