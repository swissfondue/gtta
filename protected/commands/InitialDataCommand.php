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
        sleep(3);

        // wait for job to stop
        $job = JobManager::buildId(CommunityInstallJob::ID_TEMPLATE);

        while (JobManager::isRunning($job)) {
            sleep(1);
        }
    }
}