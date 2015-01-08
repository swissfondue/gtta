<?php

/**
 * Kill job
 */
class KillJob extends BackgroundJob {
    /**
     * System job flag
     */
    const SYSTEM = true;

    /**
     * Perform job
     */
    public function perform() {
        if (!isset($this->args["pid"]) || !$this->args["pid"]) {
            return;
        }

        ProcessManager::killProcess($this->args["pid"], 10);
    }
}
