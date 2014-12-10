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
        $firstTime = false;

        if (count($args) == 1) {
            $firstTime = (bool) $args[0];
        }

        if (!$firstTime && $this->_system->status != System::STATUS_REGENERATE_SANDBOX) {
            return;
        }

        try {
            $vm = new VMManager();
            $vm->regenerate($firstTime);
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }

        if (!$firstTime) {
            SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_REGENERATE_SANDBOX);
        }
    }
} 