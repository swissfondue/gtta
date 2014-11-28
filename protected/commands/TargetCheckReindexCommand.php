<?php

/**
 * Target check sync command
 */
class TargetCheckReindexCommand extends ConsoleCommand {
    /**
     * Sync target checks
     */
    private function _process() {
        if ($this->_system->status != System::STATUS_IDLE) {
            return;
        }

        $targetCategories = TargetCheckCategory::model()->findAll();

        /** @var TargetCheckCategory $tc */
        foreach ($targetCategories as $tc) {
            $tc->reindexChecks();
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $this->start();
    }

    /**
     * Execute
     */
    protected function exec() {
        try {
            $this->_process();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), "error");
        }
    }
}
