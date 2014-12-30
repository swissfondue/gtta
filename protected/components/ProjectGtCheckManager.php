<?php

/**
 * Class ProjectGtCheckManager
 */
class ProjectGtCheckManager {
    /**
     * Start check
     * @param $id
     */
    public static function startGtCheck($projectId, $checkId) {
        $projectId = (int) $projectId;
        $checkId = (int) $checkId;
        $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
            "project_id" => $projectId,
            "gt_check_id" => $checkId,
        ));

        if (!$projectCheck) {
            return;
        }

        $now = new DateTime();

        JobManager::enqueue(JobManager::JOB_GT_AUTOMATION, array(
                "operation" => GtAutomationJob::OPERATION_START,
                "proj_id" => $projectCheck->project_id,
                "obj_id" => $projectCheck->gt_check_id,
                "started" => $now->format("Y-m-d H:i:s"),
            )
        );
    }

    /**
     * Stop check
     * @param $id
     */
    public static function stopCheck($projectId, $checkId) {
        $projectId = (int) $projectId;
        $checkId = (int) $checkId;
        $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
            "project_id" => $projectId,
            "gt_check_id" => $checkId,
        ));
        if (!$projectCheck) {
            return;
        }

        JobManager::enqueue(JobManager::JOB_GT_AUTOMATION, array(
                "operation" => GtAutomationJob::OPERATION_STOP,
                "proj_id" => $projectCheck->project_id,
                "obj_id" => $projectCheck->gt_check_id,
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
        $mask = JobManager::buildId(GtAutomationJob::JOB_ID, array(
            "operation" => "*",
            "proj_id" => "*",
            "obj_id" => "*",
        ));
        $mask .= ".pid";
        $keys = JobManager::keys($mask);

        $ids = array();

        foreach ($keys as $key) {
            if (preg_match("/project.\d+.check.\d+.(start|stop)/", $key, $match)) {
                preg_match("/project.\d+/", $match[0], $projectMatch);
                preg_match("/\d+/", $projectMatch[0], $pId);
                $projectId = $pId[0];

                preg_match("/check.\d+/", $match[0], $checkMatch);
                preg_match("/\d+/", $checkMatch[0], $cId);
                $checkId = $cId[0];

                $ids[] = array('proj_id' => $projectId, 'obj_id' => $checkId);
            }
        }

        return $ids;
    }

    /**
     * Get check started time
     * @param $id
     * @return mixed
     */
    public static function getStarted($projectId, $gtCheckId) {
        $job = JobManager::buildId(GtAutomationJob::JOB_ID, array(
            "operation" => GtAutomationJob::OPERATION_START,
            "proj_id" => $projectId,
            "obj_id" => $gtCheckId,
        ));

        return JobManager::getJobVar($job, "started");
    }
}