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
        $bm = new BackupManager();
        $bm->backup();
    }
}