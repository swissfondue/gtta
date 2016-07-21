<?php

/**
 * Class ProjectManager
 */
class ProjectManager {
    /**
     * Add issue to project
     * @param Project $project
     * @param Check $check
     * @return Issue
     */
    public function addIssue(Project $project, Check $check) {
        $issue = Issue::model()->findByAttributes([
            "check_id" => $check->id,
            "project_id" => $project->id
        ]);

        if ($issue) {
            return $issue;
        }

        $issue = new Issue();
        $issue->project_id = $project->id;
        $issue->check_id = $check->id;
        $issue->save();
        $issue->refresh();

        $tm = new TargetManager();

        foreach ($project->targets as $target) {
            $tm->addEvidence($target, $issue, false);
        }

        return $issue;
    }
}