<?php

/**
 * Migration m150314_124606_link_templates
 */
class m150314_124606_relation_templates extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->createTable(
            "relation_templates",
            array(
                "id"    => "bigserial NOT NULL",
                "name"  => "varchar(1000)",
                "relations" => "varchar",
                "PRIMARY KEY (id)"
            )
        );

        $this->createTable(
            "relation_templates_l10n",
            array(
                "relation_template_id" => "bigint NOT NULL",
                "language_id" => "bigint NOT NULL",
                "name" => "varchar(1000)",
                "PRIMARY KEY (relation_template_id, language_id)"
            )
        );
        $this->addForeignKey(
            "relation_templates_l10n_relation_template_id_fkey",
            "relation_templates_l10n",
            "relation_template_id",
            "relation_templates",
            "id",
            "CASCADE",
            "CASCADE"
        );
        $this->addForeignKey(
            "relation_templates_l10n_language_id_fkey",
            "relation_templates_l10n",
            "language_id",
            "languages",
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
        $this->dropTable("relation_templates_l10n");
        $this->dropTable("relation_templates");

        return true;
	}
}