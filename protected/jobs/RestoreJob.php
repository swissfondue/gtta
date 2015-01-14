<?php

class RestoreJob extends BackgroundJob {
    /**
     * Restore job id's template
     */
    const ID_TEMPLATE = "gtta.system.restore";

    /**
     * Perform
     */
    public function perform() {
        if (!isset($this->args['path'])) {
            throw new Exception("Invalid job params.");
        }

        $path = $this->args['path'];

        try {
            $bm = new BackupManager();
            $bm->restore($path);
        } catch (Exception $e) {
            throw new CHttpException(500, Yii::t("app", "Error restoring backup."));
        }
    }
}