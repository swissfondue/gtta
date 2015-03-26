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
            if (!isset($this->args["category_id"]) && !isset($this->args["target_id"])) {
                throw new Exception("Invalid job params.");
            }

            if (isset($this->args['target_id'])) {
                $target = Target::model()->findByPk($this->args['target_id']);

                if (!$target) {
                    throw new Exception("Target not found.");
                }

                $categories = $target->_categories;

                switch ($target->check_source_type) {
                    case Target::SOURCE_TYPE_CHECK_CATEGORIES:
                        foreach ($categories as $category) {
                            TargetManager::reindexTargetCategoryChecks($category);
                            TargetManager::updateTargetCategoryStats($category);
                        }

                        break;

                    case Target::SOURCE_TYPE_CHECKLIST_TEMPLATES:
                        $templates = $target->checklistTemplates;

                        foreach ($templates as $template) {
                            TargetManager::reindexTargetTemplateChecks($template);
                        }

                        foreach ($categories as $category) {
                            TargetManager::updateTargetCategoryStats($category);
                        }

                        break;

                    default:
                        throw new Exception("Invalid target check source type.");
                }
            } else {
                $category = CheckCategory::model()->findByPk($this->args['category_id']);

                if (!$category) {
                    throw new Exception("Category not found.");
                }

                $criteria = new CDbCriteria();
                $criteria->addCondition("project.status != :status");
                $criteria->params = array(
                    "status" => Project::STATUS_FINISHED
                );
                $targets = Target::model()->with("project")->findAll($criteria);

                $targetIds = array();

                foreach ($targets as $target) {
                    $targetIds[] = $target->id;
                }

                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array(
                    "check_category_id" => $category->id
                ));
                $criteria->addInCondition("target_id", $targetIds);
                $categories = TargetCheckCategory::model()->findAll($criteria);

                foreach ($categories as $category) {
                    TargetManager::reindexTargetCategoryChecks($category);
                    TargetManager::updateTargetCategoryStats($category);
                }
            }

            ProjectPlanner::updateAllStats();
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}