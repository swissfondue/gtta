<?php

/**
 * Stats command
 */
class StatsCommand extends ConsoleCommand {
    /**
     * Check update
     */
    private function _process() {
        if ($this->_system->status == System::STATUS_IDLE) {
            TargetCheckCategory::updateAllStats();
            ProjectPlanner::updateAllStats();
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
