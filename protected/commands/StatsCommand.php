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
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $fp = fopen(Yii::app()->params["stats"]["lockFile"], "w");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            try {
                $this->_process();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), "error");
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
