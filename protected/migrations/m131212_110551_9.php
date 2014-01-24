<?php

/**
 * Migration m131212_110551_9
 */
class m131212_110551_9 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $this->addColumn("check_scripts", "package_id", "bigint DEFAULT NULL");
        $this->addForeignKey(
            "check_scripts_package_id_fkey",
            "check_scripts",
            "package_id",
            "packages",
            "id",
            "CASCADE",
            "CASCADE"
        );

        $scripts = $this->getDbConnection()->createCommand("SELECT DISTINCT name FROM check_scripts")->query();
        $packages = $this->getDbConnection()
            ->createCommand("SELECT id, name FROM packages WHERE type = :script AND status = :installed")
            ->query(array(
                "script" => Package::TYPE_SCRIPT,
                "installed" => Package::STATUS_INSTALLED
            ));

        $packageIds = array();

        foreach ($packages as $package) {
            $packageIds[$package["name"]] = $package["id"];
        }

        foreach ($scripts as $script) {
            $name = $script["name"];
            $shortName = substr($name, 0, strrpos($name, "."));

            if (!array_key_exists($shortName, $packageIds)) {
                echo "    > unknown script $name\n";
                $this->delete("check_scripts", "name = :name", array("name" => $name));

                continue;
            }

            $this->update(
                "check_scripts",
                array("package_id" => $packageIds[$shortName]),
                "name = :name",
                array("name" => $name)
            );
        }

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->dropForeignKey("check_scripts_package_id_fkey", "check_scripts");
        $this->dropColumn("check_scripts", "package_id");

		return true;
	}
}