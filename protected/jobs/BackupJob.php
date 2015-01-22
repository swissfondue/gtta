<?php

class BackupJob extends BackgroundJob {
    /**
     * Backup job's id template
     */
    const ID_TEMPLATE = "gtta.system.backup";

    /**
     * Perform
     */
    public function perform() {
        try {
            $bm = new BackupManager();
            $bm->backup();
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());

            throw $e;
        }
    }
}