<?php

/**
 * Class RegenerateJob
 */
class RegenerateJob extends BackgroundJob {
    /**
     * System flag
     */
    const SYSTEM = false;

    /**
     * Job id
     */
    const JOB_ID = "@app@.sandbox.regenerate";

    /**
     * Perform
     */
    public function perform() {
        sleep(20);

        try {
            $vm = new VMManager();
            $vm->regenerate(false);
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
}