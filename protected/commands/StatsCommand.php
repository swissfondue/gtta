<?php

/**
 * Stats command
 */
class StatsCommand extends ConsoleCommand {
    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        if ($this->_system->status != System::STATUS_IDLE) {
            return;
        }

        TargetCheckCategory::updateAllStats();
        ProjectPlanner::updateAllStats();
    }
}
