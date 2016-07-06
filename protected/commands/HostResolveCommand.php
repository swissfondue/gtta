<?php

/**
 * Check update command
 */
class HostResolveCommand extends ConsoleCommand {
    const SETUP_COMPLETED_FLAG = "/opt/gtta/.setup-completed";

    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        if (System::model()->findByPk(1)->host_resolve) {
            HostResolveJob::enqueue();
        }
    }
}
