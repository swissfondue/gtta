<?php

/**
 * Migration m140130_145541_15
 */
class m140130_145541_15 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $files = array(
            "Africa",
            "All",
            "America",
            "Asia",
            "Europe",
            "Generic",
            "New",
            "Pacific",
            "Top 10",
            "Top 20",
        );

        $package = $this->getDbConnection()
            ->createCommand("SELECT id FROM packages WHERE name = :name AND type = :script AND status = :installed LIMIT 1")
            ->query(array(
                "name" => "dns_top_tlds",
                "script" => Package::TYPE_SCRIPT,
                "installed" => Package::STATUS_INSTALLED
            ));


        if ($package) {
            foreach ($package as $pkg) {
                $package = $pkg["id"];
                break;
            }

            $scripts = $this->getDbConnection()
                ->createCommand("SELECT id FROM check_scripts WHERE package_id = :package")
                ->query(array(
                    "package" => $package
                ));

            foreach ($scripts as $script) {
                $order = 0;

                foreach ($files as $file) {
                    $this->insert("check_inputs", array(
                        "name" => $file,
                        "sort_order" => $order,
                        "type" => CheckInput::TYPE_FILE,
                        "check_script_id" => $script["id"],
                    ));

                    $order++;
                }
            }
        }

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
		return true;
	}
}