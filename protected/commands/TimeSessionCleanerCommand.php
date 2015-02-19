<?php

/**
 * Class TimeSessionCleanerCommand
 */
class TimeSessionCleanerCommand extends ConsoleCommand {
    const INACTIVE_LIFETIME = 600; // 10 minut

    /**
     * Clear inactive sessions
     */
    private function clear() {
        $records = ProjectTime::model()->findAllByAttributes(array(
            "time" => null
        ));

        foreach ($records as $record) {
            $now = new DateTime();
            $lastAction = new DateTime($record->last_action);
            $diff = $now->getTimestamp() - $lastAction->getTimestamp();

            if ($diff > self::INACTIVE_LIFETIME) {
                $record->stop();
            }
        }
    }

    /**
     * Run
     * @param $args
     */
    protected function runLocked($args) {
        $this->clear();
    }
}