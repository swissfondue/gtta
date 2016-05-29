<?php
/**
 * Class TargetCheckReindexJob
 */
class TargetCheckReindexJob extends BackgroundJob {
    /**
     * Reindex target
     * @param Target $t
     */
    private function _reindexTarget(Target $t) {
        TargetManager::reindexTargetChecks($t);

        foreach ($t->_categories as $tcat) {
            TargetManager::updateTargetCategoryStats($tcat);
        }
    }

    /**
     * Run
     */
    public function perform() {
        try {
            if (!isset($this->args["category_id"]) &&
                !isset($this->args["target_id"]) &&
                !isset($this->args['template_id']) &&
                !isset($this->args["global_check_field_id"]) &&
                !isset($this->args["check_field_id"])
            ) {
                throw new Exception("Invalid job params.");
            }

            if (isset($this->args['target_id'])) {
                $target = Target::model()->findByPk($this->args['target_id']);

                if (!$target) {
                    throw new Exception("Target not found.");
                }

                $this->_reindexTarget($target);
            } else if (isset($this->args['category_id'])) {
                $category = CheckCategory::model()->findByPk($this->args['category_id']);

                if (!$category) {
                    throw new Exception("Category not found.");
                }

                $targetCategories = TargetCheckCategory::model()->findAllByAttributes(array(
                    "check_category_id" => $category->id
                ));

                $targetIds = array();

                foreach ($targetCategories as $tc) {
                    $targetIds[] = $tc->target_id;
                }

                $criteria = new CDbCriteria();
                $criteria->params = array(
                    "status" => Project::STATUS_FINISHED
                );
                $criteria->addCondition("project.status != :status");
                $criteria->addInCondition("t.id", $targetIds);
                $targets = Target::model()->with("project")->findAll($criteria);

                foreach ($targets as $t) {
                    $this->_reindexTarget($t);
                }
            } else if (isset($this->args["template_id"])) {
                $template = ChecklistTemplate::model()->findByPk($this->args["template_id"]);

                if (!$template) {
                    throw new Exception("Template not found.");
                }

                $targetTemplates = TargetChecklistTemplate::model()->findAllByAttributes(array(
                    "checklist_template_id" => $template->id
                ));

                $targetIds = array();

                foreach ($targetTemplates as $tc) {
                    $targetIds[] = $tc->target_id;
                }

                $criteria = new CDbCriteria();
                $criteria->params = array(
                    "status" => Project::STATUS_FINISHED
                );
                $criteria->addCondition("project.status != :status");
                $criteria->addInCondition("t.id", $targetIds);
                $targets = Target::model()->with("project")->findAll($criteria);

                foreach ($targets as $t) {
                    $this->_reindexTarget($t);
                }
            } else if (isset($this->args["global_check_field_id"])) {
                $field = GlobalCheckField::model()->findByPk($this->args["global_check_field_id"]);

                if (!$field) {
                    throw new Exception("Field not found.", 404);
                }

                FieldManager::reindexCheckFields($field);
            } else if (isset($this->args["check_field_id"])) {
                $field = CheckField::model()->findByPk($this->args["check_field_id"]);

                if (!$field) {
                    throw new Exception("Field not found.", 404);
                }

                FieldManager::reindexTargetCheckFields($field);
            }

            ProjectPlanner::updateAllStats();
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}