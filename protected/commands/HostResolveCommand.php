<?php

/**
 * Check update command
 */
class HostResolveCommand extends ConsoleCommand {
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
