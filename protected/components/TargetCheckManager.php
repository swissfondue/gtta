<?php
/**
 * Class TargetCheckManager
 */
class TargetCheckManager {
    /**
     * Start check
     * @param $id
     */
    public static function start($id) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        $now = new DateTime();

        AutomationJob::enqueue(array(
                "operation" => AutomationJob::OPERATION_START,
                "obj_id" => $targetCheck->id,
                "started" => $now->format(ISO_DATE_TIME),
            )
        );
    }

    /**
     * Stop check
     * @param $id
     */
    public static function stop($id) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        AutomationJob::enqueue(array(
                "operation" => AutomationJob::OPERATION_STOP,
                "obj_id" => $targetCheck->id,
            )
        );
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
        $keys = explode(" ", Resque::redis()->keys($mask));

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
}