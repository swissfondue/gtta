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
        CommunityInstallJob::enqueue(array("initial" => true));
    }
}