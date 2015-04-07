<?php
/**
 * Class TargetCheckReindexJob
 */
class TargetCheckReindexJob extends BackgroundJob {
    /**
     * Job id
     */
    const ID_TEMPLATE = "gtta.reindex.category.@category_id@.target.@target_id@";

    /**
     * Run
     */
    public function perform() {
        try {
            if (!isset($this->args["category_id"]) && !isset($this->args["target_id"]) && !isset($this->args['template_id'])) {
                throw new Exception("Invalid job params.");
            }

            if (isset($this->args['target_id'])) {
                $target = Target::model()->findByPk($this->args['target_id']);

                if (!$target) {
                    throw new Exception("Target not found.");
                }

                TargetManager::reindexTargetChecks($target);

                foreach ($target->_categories as $tcat) {
                    TargetManager::updateTargetCategoryStats($tcat);
                }
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
                $criteria->addCondition("project.status != :status");
                $criteria->addInCondition("t.id", $targetIds);
                $criteria->params = array(
                    "status" => Project::STATUS_FINISHED
                );
                $targets = Target::model()->with("project")->findAll($criteria);

                foreach ($targets as $t) {
                    TargetManager::reindexTargetChecks($t);

                    foreach ($t->_categories as $tcat) {
                        TargetManager::updateTargetCategoryStats($tcat);
                    }
                }
            } else if (isset($this->args['template_id'])) {
                $template = ChecklistTemplate::model()->findByPk($this->args['template_id']);

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
                $criteria->addCondition("project.status != :status");
                $criteria->addInCondition("t.id", $targetIds);
                $criteria->params = array(
                    "status" => Project::STATUS_FINISHED
                );
                $targets = Target::model()->with("project")->findAll($criteria);

                foreach ($targets as $t) {
                    TargetManager::reindexTargetChecks($t);

                    foreach ($t->_categories as $tcat) {
                        TargetManager::updateTargetCategoryStats($tcat);
                    }
                }
            }

            ProjectPlanner::updateAllStats();
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}