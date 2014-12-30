<?php
/**
 * Class TargetCheckReindexJob
 */
class TargetCheckReindexJob extends BackgroundJob {
    /**
     * System flag
     */
    const SYSTEM = false;

    /**
     * Job id
     */
    const JOB_ID = "@app@.reindex.category.@category_id@.target.@target_id@";

    /**
     * Run
     */
    public function perform() {
        if (!isset($this->args["target_id"]) || !isset($this->args["category_id"])) {
            throw new Exception("Invalid job params.");
        }

        if ($this->_system->status != System::STATUS_IDLE) {
            return;
        }

        $targetId = $this->args["target_id"];
        $categoryId = $this->args["category_id"];

        $category = TargetCheckCategory::model()->findByAttributes(array(
                "target_id" => $targetId,
                "check_category_id" => $categoryId,
            )
        );

        if (!$category) {
            throw new Exception("Category not found.");
        }

        $category->reindexChecks();

        JobManager::enqueue(JobManager::JOB_STATS, array(
            "target_id" => $targetId,
            "category_id" => $categoryId,
        ));
    }
}