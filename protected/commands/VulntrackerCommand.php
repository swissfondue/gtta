<?php

/**
 * Vulnerability Tracker class.
 */
class VulntrackerCommand extends ConsoleCommand {
    /**
     * Check vulnerabilities.
     */
    private function _checkVulns() {
        $criteria = new CDbCriteria();
        $criteria->addInCondition("t.status", array(
            Project::STATUS_OPEN,
            Project::STATUS_IN_PROGRESS,
        ));
        $criteria->together = true;

        $projects = Project::model()->with(array(
            "targets" => array(
                "with" => array(
                    "targetChecks"
                ),
            ),
            "gtChecks" => array(
                "with" => array(
                    "vuln"
                )
            ),
            "projectUsers" => array(
                "with" => "user"
            )
        ))->findAll($criteria);

        foreach ($projects as $project) {
            if ($project->vuln_overdue) {
                $overdue = new DateTime($project->vuln_overdue . ' 00:00:00');
                $today = new DateTime();
                $today->setTime(0, 0, 0);

                if ($overdue >= $today) {
                    continue;
                }
            }

            $admins = array();
            $overdued = 0;

            foreach ($project->projectUsers as $user) {
                if ($user->admin) {
                    $admins[] = $user->user;
                }
            }

            // if there is no project admins, continue to the next project
            if (!count($admins)) {
                continue;
            }

            foreach ($project->targets as $target) {
                if ($overdued > 0) {
                    break;
                }

                $totalChecks = array_merge($target->targetChecks, $target->targetCustomChecks);

                foreach ($totalChecks as $ttc) {
                    if ($ttc->vulnOverdued) {
                        $overdued++;
                        break;
                    }
                }
            }

            foreach ($project->gtChecks as $check) {
                if ($check->vuln->overdued) {
                    $overdued++;
                    break;
                }
            };

            if ($overdued > 0) {
                foreach ($admins as $user) {
                    $email = new Email();
                    $email->user_id = $user->id;

                    $email->subject = Yii::t('app', '{projectName} project has overdued vulnerabilities', array(
                        '{projectName}' => $project->name,
                    ));

                    $email->content = $this->render(
                        'application.views.email.vuln_overdue',

                        array(
                            'userName' => $user->name ? CHtml::encode($user->name) : $user->email,
                            'projectId'=> $project->id,
                            'projectName' => $project->name,
                        ),

                        true
                    );

                    $email->save();
                }

                $now = new DateTime();
                $project->vuln_overdue = $now->format("Y-m-d");
                $project->save();
            }
        }
    }

    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        for ($i = 0; $i < 10; $i++) {
            $this->_checkVulns();
            sleep(5);
        }
    }
}
