<?php

/**
 * Resque command
 */
class ResqueCommand extends CConsoleCommand {
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     * @return null
     */
    public function run($args) {
        if (count($args) != 1) {
            echo "Usage: ./yiic resque <queue>\n";
            exit(1);
        }

        $queue = $args[0];

        putenv("QUEUE=$queue");
        include("./vendor/bin/resque");
    }
}
