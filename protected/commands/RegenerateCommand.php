<?php

/**
 * Regenerate VM command
 */
class RegenerateCommand extends ConsoleCommand {
    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        if (!isset($args[0])) {
            return;
        }

        $firstTime = (bool) $args[0];

        if (!$firstTime) {
            return;
        }

        try {
            $vm = new VMManager();
            $vm->regenerate($firstTime);
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
} 