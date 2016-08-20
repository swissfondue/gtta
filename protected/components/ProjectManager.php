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

    /**
     * Initialize custom report template
     * @param Project $project
     * @param ReportTemplate $template
     */
    public function initCustomReportTemplate(Project $project, ReportTemplate $template) {
        ProjectReportSection::model()->deleteAllByAttributes([
            "project_id" => $project->id
        ]);

        foreach ($template->sections as $section) {
            $scn = new ProjectReportSection();
            $scn->project_id = $project->id;
            $scn->type = $section->type;
            $scn->sort_order = $section->sort_order;
            $scn->title = $section->title;
            $scn->content = $section->content;
            $scn->save();
        }
    }
}