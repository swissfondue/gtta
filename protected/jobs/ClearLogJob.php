<?php

class ClearLogJob extends BackgroundJob {
    /**
     * Perform
     */
    public function perform() {
        try {
            if (!isset($this->args["job"])) {
                throw new Exception("Invalid job params.");
            }

            $job = $this->args["job"];

            if (!in_array($job, JobManager::$jobs)) {
                throw new Exception("Unknown job.");
            }

            $job::clearLog();
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}