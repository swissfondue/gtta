<?php

/**
 * Class RegenerateJob
 */
class RegenerateJob extends BackgroundJob {
    /**
     * Job id
     */
    const ID_TEMPLATE = "gtta.sandbox.regenerate";

    /**
     * Perform
     */
    public function perform() {
        sleep(30);
        try {
            $vm = new VMManager();
            //$vm->regenerate(false);
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}