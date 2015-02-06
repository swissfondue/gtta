<?php

class RestoreJob extends BackgroundJob {
    /**
     * Restore job id's template
     */
    const ID_TEMPLATE = "gtta.system.restore";

    /**
     * Job tear down
     */
    public function tearDown() {
        JobManager::delKey($this->id . '.pid');
        JobManager::delKey($this->id . '.token');
        JobManager::delKey($this->id);
    }

    /**
     * Perform
     */
    public function perform() {
        try {
            if (!isset($this->args['path'])) {
                throw new Exception("Invalid job params.");
            }

            $path = $this->args['path'];
            $tmpPath = Yii::app()->params['tmpPath'] . DIRECTORY_SEPARATOR . hash('sha256', $path . rand() . time());

            try {
                FileManager::copy($path, $tmpPath);

                $bm = new BackupManager();
                $bm->restore($tmpPath);
            } catch (MatchVersionException $e) {
                $this->setVar("message", Yii::t("app", "Backup version doesn't match the system version."));
            } catch (Exception $e) {
                $this->setVar("message", Yii::t("app", "Error restoring backup."));
                throw $e;
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}