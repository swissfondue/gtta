<?php

/**
 * Dependency processor class
 */
abstract class DependencyProcessor {
    /**
     * Get matching targets.
     * @param $result
     * @param $condition
     * @return mixed
     */
    abstract protected function _get_targets($result, $tableResult, $condition);

    /**
     * Process dependencies for the given check.
     * @param ProjectGtCheck $check
     */
    public function process(ProjectGtCheck $check) {
        $dependencies = GtCheckDependency::model()->findAllByAttributes(array(
            'gt_check_id' => $check->gt_check_id
        ));

        if (!$dependencies) {
            return;
        }

        foreach ($dependencies as $dependency) {
            $targets = $this->_get_targets($check->result, $check->table_result, $dependency->condition);

            if ($targets) {
                foreach ($targets as $target) {
                    $suggestedTarget = ProjectGtSuggestedTarget::model()->findByAttributes(array(
                        'project_id' => $check->project_id,
                        'gt_module_id' => $dependency->gt_module_id,
                        'target' => $target
                    ));

                    if ($suggestedTarget) {
                        continue;
                    }

                    $suggestedTarget = new ProjectGtSuggestedTarget();
                    $suggestedTarget->project_id = $check->project_id;
                    $suggestedTarget->gt_check_id = $check->gt_check_id;
                    $suggestedTarget->gt_module_id = $dependency->gt_module_id;
                    $suggestedTarget->target = $target;
                    $suggestedTarget->save();
                }
            }
        }
    }
}
