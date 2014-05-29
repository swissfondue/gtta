<?php

/**
 * Migration m140528_120724_33
 */
class m140528_120724_33 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $language = $this->getDbConnection()->createCommand("SELECT id FROM languages WHERE code = 'en'")->execute();
        $targetChecks = $this->getDbConnection()->createCommand("SELECT target_id, check_id FROM target_checks")->query();
        $admin = $this->getDbConnection()->createCommand("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->execute();
        $cache = array();

        foreach ($targetChecks as $tc) {
            $cache[] = $tc["target_id"] . "-" . $tc["check_id"];
        }

        $checks = $this->getDbConnection()->createCommand(
            "SELECT tcc.target_id, c.id FROM target_check_categories tcc " .
            "LEFT JOIN check_categories cc ON cc.id = tcc.check_category_id " .
            "LEFT JOIN check_controls ctrl ON ctrl.check_category_id = cc.id " .
            "LEFT JOIN checks c ON c.check_control_id = ctrl.id"
        )->query();

        foreach ($checks as $c) {
            if (!$c["id"]) {
                continue;
            }

            if (in_array($c["target_id"] . "-" . $c["id"], $cache)) {
                continue;
            }

            $this->insert("target_checks", array(
                "target_id" => $c["target_id"],
                "check_id" => $c["id"],
                "status" => TargetCheck::STATUS_OPEN,
                "language_id" => $language,
                "user_id" => $admin,
                "rating" => TargetCheck::RATING_NONE,
            ));

            $cache[] = $c["target_id"] . "-" . $c["id"];
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