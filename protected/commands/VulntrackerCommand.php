<?php

/**
 * Vulnerability Tracker class.
 */
class VulntrackerCommand extends ConsoleCommand
{
    /**
     * Check vulnerabilities.
     */
    private function _checkVulns() {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.status', array(
            Project::STATUS_OPEN,
            Project::STATUS_IN_PROGRESS,
        ));
        $criteria->together = true;

        $projects = Project::model()->with(array(
            'targets' => array(
                'with' => array(
                    'vulns' => array(
                        'with' => 'targetCheck'
                    )
                ),
            ),
            'gtChecks' => array(
                'with' => array(
                    'vuln'
                )
            ),
            'project_users' => array(
                'with' => 'user'
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

            foreach ($project->project_users as $user) {
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

                foreach ($target->vulns as $vuln) {
                    if ($vuln->overdued) {
                        $overdued++;
                        break;
                    }
                }
            }

            foreach ($project->gtChecks as $check) {
                if ($check->vuln && $check->vuln->overdued) {
                    $overdued++;
                    break;
                }
            }

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
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $fp = fopen(Yii::app()->params["vulntracker"]["lockFile"], "w");
        
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            for ($i = 0; $i < 10; $i++) {
                $this->_checkVulns();
                sleep(5);
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
