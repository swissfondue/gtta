<?php

/**
 * Vulnerability Tracker class.
 */
class VulntrackerCommand extends ConsoleCommand
{
    /**
     * Check vulnerabilities.
     */
    private function _checkVulns()
    {
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
                        'joinType' => 'INNER JOIN',
                        'with'     => 'targetCheck'
                    )
                ),
            ),
            'project_users' => array(
                'with' => 'user'
            )
        ))->findAll($criteria);

        foreach ($projects as $project)
        {
            if ($project->vuln_overdue)
            {
                $overdue = new DateTime($project->vuln_overdue . ' 00:00:00');
                $today   = new DateTime();
                $today->setTime(0, 0, 0);

                if ($overdue >= $today)
                    continue;
            }

            $language = null;
            $admins   = array();
            $targets  = array();
            $overdued = 0;

            foreach ($project->project_users as $user)
                if ($user->admin)
                    $admins[] = $user->user;

            // if there is no project admins, continue to the next project
            if (!count($admins))
                continue;

            foreach ($project->targets as $target)
            {
                $targetObject = array(
                    'target'   => $target,
                    'overdued' => 0
                );

                foreach ($target->vulns as $vuln)
                    if ($vuln->overdued)
                    {
                        $targetObject['overdued']++;

                        if (!$language)
                            $language = $vuln->targetCheck->language_id;
                    }

                if ($targetObject['overdued'])
                {
                    $targets[] = $targetObject;
                    $overdued += $targetObject['overdued'];
                }
            }

            if (!$language)
            {
                $language = Language::model()->findByAttributes(array(
                    'default' => true
                ));

                if ($language)
                    $language = $language->id;
            }

            if ($overdued > 0)
            {
                foreach ($admins as $user)
                {
                    $email = new Email();
                    $email->user_id = $user->id;

                    $email->subject = Yii::t('app', '{projectName} project has overdued vulnerabilities', array(
                        '{projectName}' => $project->name,
                    ));

                    $email->content = $this->render(
                        'application.views.email.vuln_overdue',

                        array(
                            'userName'    => $user->name ? CHtml::encode($user->name) : $user->email,
                            'projectId'   => $project->id,
                            'projectName' => $project->name,
                            'targets'     => $targets
                        ),

                        true
                    );

                    $email->save();
                }

                $project->vuln_overdue = new CDbExpression('NOW()');
                $project->save();
            }
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args)
    {
        $fp = fopen(Yii::app()->params['vulntracker']['lockFile'], 'w');
        
        if (flock($fp, LOCK_EX | LOCK_NB))
        {
            for ($i = 0; $i < 10; $i++)
            {
                $this->_checkVulns();
                sleep(5);
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
