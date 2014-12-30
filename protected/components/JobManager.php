<?php
/**
 * Class JobManager
 */
class JobManager {
    /**
     * Queue
     */
    const QUEUE_SYSTEM              = "system";
    const QUEUE_WORKER              = "worker";

    /**
     * Jobs
     */
    const JOB_AUTOMATION            = 'AutomationJob';
    const JOB_COMMUNITY_INSTALL     = 'CommunityInstallJob';
    const JOB_COMMUNITY_SHARE       = 'CommunityShareJob';
    const JOB_EMAIL                 = 'EmailJob';
    const JOB_GT_AUTOMATION         = 'GtAutomationJob';
    const JOB_PACKAGE               = 'PackageJob';
    const JOB_REGENERATE            = 'RegenerateJob';
    const JOB_STATS                 = 'StatsJob';
    const JOB_TARGET_CHECK_REINDEX  = 'TargetCheckReindexJob';
    const JOB_UPDATE                = 'UpdateJob';
    const JOB_MODIFIED_PACKAGES     = 'ModifiedPackagesJob';
    const JOB_KILL                  = 'KillJob';

    /**
     * Checks if job is running
     * @param $job
     * @return bool
     */
    public static function isRunning($job) {
        return in_array(self::getStatus($job), array(Resque_Job_Status::STATUS_RUNNING, Resque_Job_Status::STATUS_WAITING));
    }

    /**
     * Create a job
     * @param $job
     * @param $args
     */
    public static function enqueue($class, $args = array()) {
        $job = null;
        $queue = $class::SYSTEM ? self::QUEUE_SYSTEM : self::QUEUE_WORKER;
        $job = self::buildId($class::JOB_ID, $args);
        $token = Resque::enqueue($queue, $class, array_merge($args, array(
            "id" => $job
        )), true);

        Resque::redis()->set("$job.token", $token);

        if (isset($args['started'])) {
            Resque::redis()->set("$job.started", $args['started']);
        }
    }

    /**
     * Returns job status
     * @param $job
     * @return Resque_Job_Status
     */
    private function getStatus($job) {
        $token = Resque::redis()->get("$job.token");

        if (!$token) {
            return Resque_Job_Status::STATUS_COMPLETE;
        }

        $status = new Resque_Job_Status($token);

        return $status->get();
    }

    /**
     * Build job ID
     * @return string
     */
    public static function buildId($jobTemplate, $values = array()) {
        if (!$jobTemplate) {
            return null;
        }

        $jobTemplate = str_replace('@app@', Yii::app()->name, $jobTemplate);

        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $pattern = '@' . $key . '@';
                $jobTemplate = str_replace($pattern, $value, $jobTemplate);
            }
        }

        return $jobTemplate;
    }

    /**
     * Returns pid of job
     * @param $job
     * @return mixed
     */
    public static function getPid($job) {
        return Resque::redis()->get("$job.pid");
    }

    /**
     * Return job's param
     * @param $job
     * @return mixed
     */
    public static function getJobVar($job, $var) {
        return Resque::redis()->get("$job.$var");
    }

    /**
     * Return keys as array
     * @param $mask
     * @return array
     */
    public static function keys($mask) {
        $keys = Resque::redis()->keys($mask);
        $keys = explode(' ', $keys);

        return $keys;
    }
}