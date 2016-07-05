<?php

/**
 * Check update command
 */
class HostResolveCommand extends ConsoleCommand {
    const SETUP_COMPLETED_FLAG = "/opt/gtta/.setup-completed";

    /**
     * Resolve hostname
     */
    private function _resolve($hostname) {
        return gethostbyname($hostname);
    }

    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        if (System::model()->findByPk(1)->host_resolve) {
            foreach (Target::model()->findAll() as $target) {
                $target->ip = $this->_resolve($target->host);
                $target->save();
            }
        }
    }
}
