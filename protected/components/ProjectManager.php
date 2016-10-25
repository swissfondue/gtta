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

    /**
     * Import nessus report
     * @param Project $project
     * @param NessusMapping $mapping
     * @throws Exception
     */
    public function importNessusReport(Project $project, NessusMapping $mapping) {
        $nrm = new NessusReportManager();
        $tcm = new TargetCheckManager();

        try {
            $parsed = $nrm->parse(Yii::app()->params["tmpPath"] . DS . $project->import_filename);
            $language = System::model()->findByPk(1)->language;

            try {
                foreach ($parsed["hosts"] as $host) {
                    $target = new Target();
                    $target->project_id = $project->id;
                    $target->host = trim($host["name"]);
                    $target->save();
                    $target->refresh();

                    $admin = $target->project->admin ? $target->project->admin : User::getAdmin();

                    foreach ($host["vulnerabilities"] as $v) {
                        $vuln = NessusMappingVuln::model()->findByAttributes([
                            "nessus_mapping_id" => $mapping->id,
                            "nessus_plugin_id" => $v["plugin_id"],
                            "active" => true
                        ]);

                        if ($vuln && $vuln->check) {
                            $data = [
                                "target_id" => $target->id,
                                "user_id" => $admin->id,
                                "language_id" => $language->id,
                                "rating" => $vuln->rating ? $vuln->rating : TargetCheck::RATING_NONE
                            ];

                            if ($vuln->result) {
                                $data["result"] = $vuln->result->result;
                            }

                            if ($vuln->solution) {
                                $data["solutions"] = [$vuln->solution->id];
                            }

                            $tcm->create($vuln->check, $data);
                        }
                    }

                    ReindexJob::enqueue(["target_id" => $target->id]);
                    StatsJob::enqueue(["target_id" => $target->id]);
                }
            } catch (Exception $e) {
                throw new Exception("Import failed.");
            }
        } catch (Exception $e) {
            throw new Exception("Nessus import failed.");
        }
    }
}