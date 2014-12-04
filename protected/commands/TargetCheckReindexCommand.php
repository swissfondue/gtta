<?php

/**
 * Target check sync command
 */
class TargetCheckReindexCommand extends ConsoleCommand {
    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        if ($this->_system->status != System::STATUS_IDLE) {
            return;
        }

        $targetCategories = TargetCheckCategory::model()->findAll();

        /** @var TargetCheckCategory $tc */
        foreach ($targetCategories as $tc) {
            $tc->reindexChecks();
        }
    }
}
