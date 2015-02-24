<?php

/**
 * Class ProjectGtCheckManager
 */
class ProjectGtCheckManager {
    /**
     * Start check
     * @param $id
     */
    public static function start($projectId, $checkId) {
        $projectId = (int) $projectId;
        $checkId = (int) $checkId;
        $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
            "project_id" => $projectId,
            "gt_check_id" => $checkId,
        ));

        if (!$projectCheck) {
            throw new Exception("Check not found.");
        }

        $now = new DateTime();

        GtAutomationJob::enqueue(array(
                "operation" => GtAutomationJob::OPERATION_START,
                "proj_id" => $projectCheck->project_id,
                "obj_id" => $projectCheck->gt_check_id,
                "started" => $now->format(ISO_DATE_TIME),
            )
        );
    }

    /**
     * Stop check
     * @param $id
     */
    public static function stop($projectId, $checkId) {
        $projectId = (int) $projectId;
        $checkId = (int) $checkId;
        $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
            "project_id" => $projectId,
            "gt_check_id" => $checkId,
        ));
        if (!$projectCheck) {
            throw new Exception("Check not found.");
        }

        GtAutomationJob::enqueue(array(
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
    public static function getRunning() {
        $mask = JobManager::buildId(GtAutomationJob::ID_TEMPLATE, array(
            "operation" => "*",
            "proj_id" => "*",
            "obj_id" => "*",
        ));
        $mask .= ".pid";
        $keys = explode(" ", Resque::redis()->keys($mask));

        $ids = array();

        foreach ($keys as $key) {
            if (preg_match("/project.\d+.check.\d+.start/", $key, $match)) {
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
    public static function getStartTime($projectId, $gtCheckId) {
        $job = JobManager::buildId(GtAutomationJob::ID_TEMPLATE, array(
            "operation" => GtAutomationJob::OPERATION_START,
            "proj_id" => $projectId,
            "obj_id" => $gtCheckId,
        ));

        return JobManager::getVar($job, "started");
    }
}