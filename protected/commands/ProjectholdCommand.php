<?php

/**
 * Project hold tracker class.
 */
class ProjectholdCommand extends ConsoleCommand
{
    /**
     * Process on-hold projects
     */
    private function _process() {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("t.status" => Project::STATUS_ON_HOLD));
        $criteria->together = true;

        $projects = Project::model()->findAll($criteria);

        foreach ($projects as $project) {
            if ($project->start_date) {
                $start = new DateTime($project->start_date . " 00:00:00");
                $today = new DateTime();
                $today->setTime(0, 0, 0);

                if ($start > $today) {
                    continue;
                }
            }

            $project->status = Project::STATUS_OPEN;
            $project->save();
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $fp = fopen(Yii::app()->params["projectHold"]["lockFile"], "w");
        
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            $this->_process();
            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
