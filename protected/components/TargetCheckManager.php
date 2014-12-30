<?php
/**
 * Class TargetCheckManager
 */
class TargetCheckManager {
    /**
     * Start check
     * @param $id
     */
    public static function startCheck($id) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            return;
        }

        $now = new DateTime();

        JobManager::enqueue(JobManager::JOB_AUTOMATION, array(
                "operation" => AutomationJob::OPERATION_START,
                "obj_id" => $targetCheck->id,
                "started" => $now->format("Y-m-d H:i:s"),
            )
        );
    }

    /**
     * Stop check
     * @param $id
     */
    public static function stopCheck($id) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            return;
        }

        JobManager::enqueue(JobManager::JOB_AUTOMATION, array(
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
    public static function runningCheckIds() {
        $mask = JobManager::buildId(AutomationJob::JOB_ID, array(
            "operation" => "*",
            "obj_id" => "*",
        ));
        $mask .= ".pid";
        $keys = JobManager::keys($mask);

        $ids = array();

        foreach ($keys as $key) {
            if (preg_match("/check.(start|stop).[\d]+/", $key, $match)) {
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
    public static function getStarted($id) {
        $job = JobManager::buildId(AutomationJob::JOB_ID, array(
            "operation" => AutomationJob::OPERATION_START,
            "obj_id" => $id,
        ));

        return JobManager::getJobVar($job, "started");
    }
}