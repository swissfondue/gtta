<?php

/**
 * Automation class.
 */
class AutomationCommand extends ConsoleCommand
{
    /**
     * Process starting checks.
     */
    private function _processStartingChecks()
    {
        $checks = TargetCheck::model()->findAllByAttributes(array(
            'status' => TargetCheck::STATUS_IN_PROGRESS,
            'pid'    => null
        ));

        foreach ($checks as $check)
        {
            $this->_backgroundExec(
                Yii::app()->params['yiicPath'] . '/' .
                ( $this->_isWindows() ? 'yiic.bat' : 'yiic' ) .
                ' automation ' . $check->target_id . ' ' . $check->check_id
            );
        }
    }

    /**
     * Process stopping checks.
     */
    private function _processStoppingChecks()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('status = :status');
        $criteria->params = array( 'status' => TargetCheck::STATUS_STOP );

        $checks = TargetCheck::model()->findAll($criteria);

        foreach ($checks as $check)
        {
            $fileOutput = NULL;

            if ($check->pid)
            {
                $this->_killProcess($check->pid);
                $fileOutput = file_get_contents(Yii::app()->params['automation']['tempPath'] . '/' . $check->result_file);
            }

            $check->result = $fileOutput ? $fileOutput : 'No output.';
            $check->status = TargetCheck::STATUS_FINISHED;
            $check->pid    = NULL;

            $check->save();
        }
    }

    /**
     * Process running checks.
     */
    private function _processRunningChecks()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('pid IS NOT NULL');
        $criteria->addInCondition('status', array( TargetCheck::STATUS_IN_PROGRESS, TargetCheck::STATUS_STOP ));

        $checks = TargetCheck::model()->findAll($criteria);

        foreach ($checks as $check)
        {
            $fileOutput    = file_get_contents(Yii::app()->params['automation']['tempPath'] . '/' . $check->result_file);
            $check->result = $fileOutput;

            // if task died for some reason
            if (!$this->_isRunning($check->pid))
            {
                $check->pid = NULL;

                if (!$check->result)
                    $check->result = 'No output.';

                $check->status = TargetCheck::STATUS_FINISHED;
            }

            $check->save();
        }
    }

    /**
     * Check if process with given PID is running.
     */
    private function _isRunning($pid)
    {
        if ($this->_isWindows())
        {
            $output = array();
            exec('tasklist.exe', $output);

            foreach ($output as $line)
            {
                if (strpos('Image Name', $line) === 0 || strpos('===', $line) === 0)
                    continue;

                $matches = false;

                preg_match('/(.*)\s+(\d+).*$/', $line);

                if ($matches[2] == $pid)
                    return true;
            }

            return false;
        }
        else
            return file_exists('/proc/' . $pid);
    }

    /**
     * Kill process.
     */
    private function _killProcess($pid)
    {
        exec($this->_isWindows() ? 'taskkill /PID ' . $pid . ' /F /T' : 'kill -9 ' . $pid);
        return $this->_isRunning($pid);
    }

    /**
     * Check OS.
     */
    private function _isWindows()
    {
        return substr(php_uname(), 0, 7) == 'Windows';
    }

    /**
     * Run a background command.
     */
    private function _backgroundExec($cmd)
    {
        if ($this->_isWindows())
        {
            $shell = new COM('WScript.Shell');
            $shell->Run($cmd, 0, false);
        }
        else
            exec($cmd . ' > /dev/null 2>&1 &');
    }

    /**
     * Generate a file name for automated checks.
     */
    private function _generateFileName()
    {
        $name = null;

        while (true)
        {
            $name = hash('sha256', rand() . time() . rand());

            $check = TargetCheckInput::model()->findByAttributes(array(
                'file' => $name
            ));

            if ($check)
                continue;

            $criteria = new CDbCriteria();
            $criteria->addCondition('target_file = :file OR result_file = :file');
            $criteria->params = array( 'file' => $name );

            $check = TargetCheck::model()->find($criteria);

            if ($check)
                continue;

            break;
        }

        return $name;
    }

    /**
     * Check starter.
     */
    private function _startCheck($targetId, $checkId)
    {
        $check = TargetCheck::model()->with('check', 'language')->findByAttributes(array(
            'status'    => TargetCheck::STATUS_IN_PROGRESS,
            'pid'       => null,
            'target_id' => $targetId,
            'check_id'  => $checkId
        ));

        $target = Target::model()->findByPk($targetId);

        if (!$check)
            return;

        $language = $check->language;

        if (!$language)
            $language = Language::model()->findByAttributes(array(
                'default' => true
            ));

        Yii::app()->language = $language->code;

        $tempPath    = Yii::app()->params['automation']['tempPath'];
        $scriptsPath = Yii::app()->params['automation']['scriptsPath'];

        try
        {
            if (!file_exists($scriptsPath . '/' . $check->check->script))
                throw new Exception(Yii::t('app', 'Script file not found.'));

            $script       = $check->check->script;
            $extension    = pathinfo($script, PATHINFO_EXTENSION);
            $interpreter  = null;
            $interpreters = Yii::app()->params['automation']['interpreters'];

            if (isset($interpreters[$extension]))
                $interpreter = $interpreters[$extension];

            if (!$interpreter || !file_exists($interpreter['path']))
                throw new Exception(Yii::t('app', 'Interpreter not found.'));

            $check->pid         = getmypid();
            $check->started     = new CDbExpression('NOW()');
            $check->target_file = $this->_generateFileName();
            $check->result_file = $this->_generateFileName();
            $check->save();

            // create target file
            $targetFile = fopen($tempPath . '/' . $check->target_file, 'w');

            // base data
            fwrite($targetFile, ($check->override_target ? $check->override_target : $target->host) . "\n");
            fwrite($targetFile, $check->protocol       . "\n");
            fwrite($targetFile, $check->port           . "\n");
            fwrite($targetFile, $check->language->code . "\n");

            // directories
            fwrite($targetFile, $scriptsPath . "\n");
            fwrite($targetFile, $tempPath    . "\n");
            fwrite($targetFile, $interpreter['path']     . "\n");
            fwrite($targetFile, $interpreter['basePath'] . "\n");

            fclose($targetFile);

            // create empty result file
            $resultFile = fopen($tempPath . '/' . $check->result_file, 'w');
            fclose($resultFile);

            // create input files
            $inputs = TargetCheckInput::model()->with('input')->findAllByAttributes(
                array(
                    'target_id' => $targetId,
                    'check_id'  => $checkId,
                ),
                array(
                    'order' => 'input.sort_order ASC'
                )
            );

            $inputFiles = array();

            foreach ($inputs as $input)
            {
                $input->file = $this->_generateFileName();
                $input->save();

                $inputFile = fopen($tempPath . '/' . $input->file, 'w');
                fwrite($inputFile, $input->value . "\n");
                fclose($inputFile);

                $inputFiles[] = $input->file;
            }

            chdir($scriptsPath);

            $command = array(
                $interpreter['path'],
                $script,
                $tempPath . '/' . $check->target_file,
                $tempPath . '/' . $check->result_file,
            );

            foreach ($inputFiles as $input)
                $command[] = $tempPath . '/' . $input;

            $command = implode(' ', $command);

            $output = array();
            exec($command . ' 2>&1', $output);

            $fileOutput = file_get_contents($tempPath . '/' . $check->result_file);

            $check = TargetCheck::model()->findByAttributes(array(
                'target_id' => $targetId,
                'check_id'  => $checkId
            ));

            $check->pid    = NULL;
            $check->result = $fileOutput ? $fileOutput : implode("\n", $output);

            if (!$check->result)
                $check->result = Yii::t('app', 'No output.');

            $check->status = TargetCheck::STATUS_FINISHED;
            $check->save();

            date_default_timezone_set(Yii::app()->params['timeZone']);

            $started  = new DateTime($check->started);
            $interval = time() - $started->getTimestamp();

            if ($interval > Yii::app()->params['automation']['minNotificationInterval'])
            {
                $user = User::model()->findByPk($check->user_id);

                $email = new Email();
                $email->user_id = $user->id;

                $email->subject = Yii::t('app', '{checkName} check has been finished', array(
                    '{checkName}' => $check->check->localizedName
                ));

                $email->content = $this->render(
                    'application.views.email.check',

                    array(
                        'userName'   => $user->name ? CHtml::encode($user->name) : $user->email,
                        'projectId'  => $target->project_id,
                        'targetId'   => $target->id,
                        'categoryId' => $check->check->control->check_category_id,
                        'checkId'    => $check->check_id,
                        'checkName'  => $check->check->localizedName,
                        'targetHost' => $target->host
                    ),

                    true
                );

                $email->save();
            }
        }
        catch (Exception $e)
        {
            $check->automationError($e->getMessage());
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args)
    {
        // start checks
        if (count($args) > 0)
        {
            if (count($args) != 2)
            {
                echo 'Invalid number of arguments.' . "\n";
                exit();
            }

            $targetId = (int) $args[0];
            $checkId  = (int) $args[1];

            if ($targetId && $checkId)
                $this->_startCheck($targetId, $checkId);
            else
                echo 'Invalid arguments.' . "\n";

            exit();
        }

        // one instance check
        $fp = fopen(Yii::app()->params['automation']['lockFile'], 'w');
        
        if (flock($fp, LOCK_EX | LOCK_NB))
        {
            for ($i = 0; $i < 10; $i++)
            {
                $this->_processStartingChecks();
                $this->_processStoppingChecks();
                $this->_processRunningChecks();

                sleep(5);
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
