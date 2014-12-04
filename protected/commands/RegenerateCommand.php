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
        if ($this->_system->status != System::STATUS_REGENERATE_SANDBOX) {
            return;
        }

        try {
            $vm = new VMManager();
            $vm->regenerate();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }

        SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_REGENERATE_SANDBOX);
    }
} 