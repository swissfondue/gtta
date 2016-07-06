<?php
/**
 * Class JobManager
 */
class JobManager {
    /**
     * Jobs list
     * @var array
     */
    public static $jobs = array(
        "AutomationJob",
        "BackupJob",
        "ClearLogJob",
        "ChainJob",
        "CommunityInstallJob",
        "CommunityShareJob",
        "CommunityUpdateStatusJob",
        "EmailJob",
        "GitJob",
        "ModifiedPackagesJob",
        "PackageJob",
        "RegenerateJob",
        "RestoreJob",
        "StatsJob",
        "ReindexJob",
        "UpdateJob",
    );
    /**
     * Checks if job is running
     * @param $job
     * @return bool
     */
    public static function isRunning($job) {
        return in_array(self::getStatus($job), array(Resque_Job_Status::STATUS_RUNNING, Resque_Job_Status::STATUS_WAITING));
    }

    /**
     * Returns job status
     * @param $job
     * @return Resque_Job_Status
     */
    public static function getStatus($job) {
        $token = Resque::redis()->get("$job.token");

        if (!$token) {
            return Resque_Job_Status::STATUS_COMPLETE;
        }

        $status = new Resque_Job_Status($token);

        return $status->get();
    }

    /**
     * Forcefully stop the job
     * @param $job
     */
    public static function stop($job) {
        $pid = self::getPid($job);

        if ($pid) {
            ProcessManager::killProcess($pid);
        }

        $token = Resque::redis()->get("$job.token");

        if (!$token) {
            return;
        }

        $status = new Resque_Job_Status($token);
        $status->update(Resque_Job_Status::STATUS_COMPLETE);
    }

    /**
     * Build job ID
     * @return string
     */
    public static function buildId($jobTemplate, $values = array()) {
        if (!$jobTemplate) {
            return null;
        }

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
    public static function getVar($job, $var) {
        return Resque::redis()->get("$job.$var");
    }

    /**
     * Delete Redis key
     * @param $key
     */
    public static function delKey($key) {
        $key = str_replace('resque:', '', $key);
        Resque::redis()->del($key);
    }

    /**
     * Set redis key value
     * @param $key
     * @param $value
     */
    public static function setKeyValue($key, $value) {
        Resque::redis()->set($key, $value);
    }

    /**
     * Returns redis key value
     * @param $key
     * @return mixed
     */
    public static function getKeyValue($key) {
        return Resque::redis()->get($key);
    }
}
