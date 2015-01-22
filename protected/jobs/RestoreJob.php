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

            try {
                $bm = new BackupManager();
                $bm->restore($path);
            } catch (MatchVersionException $e) {
                $this->setVar("message", Yii::t("app", "Backup version doesn't match with the system version."));
            } catch (Exception $e) {
                $this->setVar("message", Yii::t("app", "Error restoring backup."));
                throw $e;
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());

            throw $e;
        }
    }
}