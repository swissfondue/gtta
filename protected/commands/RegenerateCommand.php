<?php

/**
 * Regenerate VM command
 */
class RegenerateCommand extends ConsoleCommand {
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        // one instance check
        if ($this->lock()) {
            if ($this->_system->status == System::STATUS_REGENERATE_SANDBOX) {
                try {
                    $vm = new VMManager();
                    $vm->regenerate();
                } catch (Exception $e) {
                    Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                }

                SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_REGENERATE_SANDBOX);
            }

            $this->unlock();
        }

        $this->closeLockHandle();
    }
} 