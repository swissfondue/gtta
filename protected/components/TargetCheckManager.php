<?php
/**
 * Class TargetCheckManager
 */
class TargetCheckManager {
    /**
     * Target check create
     * @param $data
     * @return TargetCheck
     * @throws Exception
     */
    public static function create($data) {
        $targetCheck = new TargetCheck();

        if (!isset($data["target_id"]) ||
            !isset($data["check_id"]) ||
            !isset($data["user_id"]) ||
            !isset($data["language_id"])) {
            throw new Exception("Invalid check data.", 500);
        }

        try {
            $targetCheck->target_id = $data["target_id"];
            $targetCheck->check_id = $data["check_id"];
            $targetCheck->user_id = $data["user_id"];
            $targetCheck->language_id = $data["language_id"];
            $targetCheck->status = isset($data["status"]) ? $data["status"] : TargetCheck::STATUS_OPEN;

            if (isset($data["rating"])) {
                $targetCheck->rating = $data["rating"];
            }

            if (isset($data["result"])) {
                $targetCheck->setResult($data["result"]);
            }

            $targetCheck->save();
        } catch (Exception $e) {
            throw new Exception("Can't create check.");
        }


        return $targetCheck;
    }

    /**
     * Start check
     * @param $id
     */
    public static function start($id, $chain=false) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        $now = new DateTime();

        $params = [
            "operation" => AutomationJob::OPERATION_START,
            "obj_id" => $targetCheck->id,
            "started" => $now->format(ISO_DATE_TIME),
        ];

        if ($chain) {
            $params["chain"] = true;
        }

        AutomationJob::enqueue($params);
    }

    /**
     * Stop check
     * @param $id
     * @throws Exception
     */
    public static function stop($id) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        if ($targetCheck->isRunning) {
            AutomationJob::enqueue(array(
                "operation" => AutomationJob::OPERATION_STOP,
                "obj_id" => $targetCheck->id,
            ));
        }
    }

    /**
     * Get running check ids
     * @param $id
     * @return array
     * @throws Exception
     */
    public static function getRunning() {
        $mask = JobManager::buildId(AutomationJob::ID_TEMPLATE, array(
            "operation" => "*",
            "obj_id" => "*",
        ));
        $mask .= ".pid";
        $keys = Resque::redis()->keys($mask);

        if (!is_array($keys)) {
            $keys = explode(" ", $keys);
        }

        $ids = array();

        foreach ($keys as $key) {
            if (preg_match("/check.start.\d+/", $key, $match)) {
                preg_match("/\d+/", $match[0], $id);
                $ids[] = $id[0];
            }
        }

        return $ids;
    }

    /**
     * Get check started time
     * @param $id
     * @return mixed
     */
    public static function getStartTime($id) {
        $job = JobManager::buildId(AutomationJob::ID_TEMPLATE, array(
            "operation" => AutomationJob::OPERATION_START,
            "obj_id" => $id,
        ));

        return JobManager::getVar($job, "started");
    }

    /**
     * Reindex target check fields
     * @param CheckField $checkField
     * @throws Exception
     */
    public static function reindexFields(CheckField $checkField) {
        $language = Language::model()->findByAttributes([
            "user_default" => true
        ]);

        if (!$language) {
            $language = System::model()->findByPk(1)->language;
        }

        foreach ($checkField->check->targetChecks as $targetCheck) {
            $targetCheckField = TargetCheckField::model()->findByAttributes([
                "check_field_id" => $checkField->id,
                "target_check_id" => $targetCheck->id
            ]);

            // if readonly field -> update value
            if ($targetCheckField) {
                if ($checkField->global->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) {
                    $targetCheckField->value = $checkField->getValue($language->id);
                    $targetCheckField->save();
                }
            } else {
                $tcf = new TargetCheckField();
                $tcf->target_check_id = $targetCheck->id;
                $tcf->check_field_id = $checkField->id;
                $tcf->value = $checkField->getValue($language->id);
                $tcf->save();
            }
        }
    }
}