<?php

/**
 * Class InitialDataCommand
 */
class InitialDataCommand extends ConsoleCommand {
    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        try {
            CommunityInstallJob::enqueue(array(
                "initial" => true
            ));
        } catch (Exception $e) {
            // pass
        }
    }
}