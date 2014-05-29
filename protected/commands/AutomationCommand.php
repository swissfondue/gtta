<?php

/**
 * Automation class.
 */
class AutomationCommand extends ConsoleCommand {
    /**
     * Process starting checks.
     */
    private function _processStartingChecks() {
        $checks = TargetCheck::model()->findAllByAttributes(array(
            'status' => TargetCheck::STATUS_IN_PROGRESS,
            'pid' => null
        ));

        foreach ($checks as $check) {
            ProcessManager::backgroundExec(
                Yii::app()->params['yiicPath'] . '/yiic automation ' . $check->id
            );
        }
    }

    /**
     * Translate pid into the sandbox PID
     * @param $pid
     * @return int process id
     */
    private function _translatePid($pid) {
        $vm = new VMManager();

        $pidFileName = $vm->virtualizePath(Yii::app()->params['automation']['pidsPath'] . '/' . $pid);

        if (!file_exists($pidFileName)) {
            return 0;
        }

        return (int) file_get_contents($pidFileName);
    }

    /**
     * Process stopping checks.
     */
    private function _processStoppingChecks() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('status = :status');
        $criteria->params = array('status' => TargetCheck::STATUS_STOP);
        $checks = TargetCheck::model()->findAll($criteria);

        foreach ($checks as $check) {
            $fileOutput = null;
            $vm = new VMManager();

            if ($check->pid) {
                $vm->killProcessGroup($this->_translatePid($check->pid));
                sleep(5);

                $outFileName = $vm->virtualizePath(
                    Yii::app()->params['automation']['filesPath'] . '/' . $check->result_file
                );

                if (file_exists($outFileName)) {
                    $fileOutput = file_get_contents($outFileName);
                }
            }

            if (!$check->result) {
                $check->result = '';
            }

            $check->result .= $fileOutput ? $fileOutput : 'No output.';
            $check->status = TargetCheck::STATUS_FINISHED;
            $check->pid = null;
            $check->save();
        }
    }

    /**
     * Process running checks.
     */
    private function _processRunningChecks() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('pid IS NOT NULL');
        $criteria->addInCondition('status', array(TargetCheck::STATUS_IN_PROGRESS, TargetCheck::STATUS_STOP));
        $checks = TargetCheck::model()->findAll($criteria);

        foreach ($checks as $check) {
            // if task died for some reason
            if (!ProcessManager::isRunning($check->pid)) {
                $check->pid = null;

                if (!$check->result) {
                    $check->result = 'No output.';
                }

                $check->status = TargetCheck::STATUS_FINISHED;
            }

            $check->save();
        }
    }

    /**
     * Generate a file name for automated checks.
     */
    private function _generateFileName() {
        $name = null;

        while (true) {
            $name = hash('sha256', rand() . time() . rand());
            $check = TargetCheckInput::model()->findByAttributes(array(
                'file' => $name
            ));

            if ($check) {
                continue;
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('target_file = :file OR result_file = :file');
            $criteria->params = array('file' => $name);
            $check = TargetCheck::model()->find($criteria);

            if ($check) {
                continue;
            }

            break;
        }

        return $name;
    }

    /**
     * Send notification
     * @param $check
     * @param $target
     */
    private function _sendNotification($check, $target) {
        $user = User::model()->findByPk($check->user_id);

        if ($user->send_notifications) {
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

    /**
     * Create check files
     * @param $check
     * @param $interpreter
     * @param $target
     * @param $script
     * @return array
     */
    private function _createCheckFiles($check, $target, $script) {
        $vm = new VMManager();
        $filesPath = $vm->virtualizePath(Yii::app()->params["automation"]["filesPath"]);

        // create target file
        $targetFile = @fopen($filesPath . '/' . $check->target_file, 'w');

        if (!$targetFile) {
            throw new VMNotFoundException("Sandbox is not running, please regenerate it.");
        }

        $targetHost = $check->override_target ? $check->override_target : $target->host;
        $port = $check->port;

        if (preg_match('/:(\d+)$/', $targetHost, $matches)) {
            $port = $matches[1];
            $targetHost = substr($targetHost, 0, strrpos($targetHost, ":"));
        }

        // base data
        fwrite($targetFile, $targetHost . "\n");
        fwrite($targetFile, $check->protocol . "\n");
        fwrite($targetFile, $port . "\n");
        fwrite($targetFile, $check->language->code . "\n");
        fclose($targetFile);

        // create empty result file
        $resultFile = @fopen($filesPath . '/' . $check->result_file, 'w');

        if (!$resultFile) {
            throw new VMNotFoundException("Sandbox is not running, please regenerate it.");
        }

        fclose($resultFile);

        $inputs = CheckInput::model()->findAllByAttributes(array(
            'check_script_id' => $script->id
        ));

        $inputIds = array();

        foreach ($inputs as $input) {
            $inputIds[] = $input->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('target_check_id' => $check->id));
        $criteria->addInCondition('check_input_id', $inputIds);
        $criteria->order = 'input.sort_order ASC';

        // create input files
        $inputs = TargetCheckInput::model()->with('input')->findAll($criteria);
        $inputFiles = array();

        foreach ($inputs as $input) {
            $input->file = $this->_generateFileName();
            $input->save();

            $value = $input->value;

            if ($input->input->type == CheckInput::TYPE_FILE) {
                if (!$value) {
                    continue;
                }

                $value = $input->input->getFileData();
            } else {
                $value = $input->value;
            }

            $inputFile = @fopen($filesPath . '/' . $input->file, 'w');

            if (!$inputFile) {
                throw new VMNotFoundException("Sandbox is not running, please regenerate it.");
            }

            fwrite($inputFile, $value . "\n");
            fclose($inputFile);

            $inputFiles[] = $input->file;
        }

        return $inputFiles;
    }

    /**
     * Get tables from response
     */
    private function _getTables(&$check) {
        $tablePos = strpos($check->result, '<' . ResultTable::TAG_MAIN);

        if ($tablePos !== false) {
            if (!$check->table_result) {
                $check->table_result = "";
            }

            $check->table_result .= substr($check->result, $tablePos);
            $check->result = substr($check->result, 0, $tablePos);
        }
    }

    /**
     * Get images from response
     */
    private function _getImages(&$check) {
        $imagePos = strpos($check->result, "<" . AttachedImage::TAG_MAIN);

        while ($imagePos !== false) {
            $imageEndPos = strpos($check->result, ">", $imagePos);

            if ($imageEndPos === false) {
                break;
            }

            $imageTag = substr($check->result, $imagePos, $imageEndPos + 1 - $imagePos);
            $check->result = substr($check->result, 0, $imagePos) . substr($check->result, $imageEndPos + 1);

            $image = new AttachedImage();
            $image->parse($imageTag);

            if ($image->src) {
                $fileInfo = finfo_open();
                $mimeType = finfo_file($fileInfo, $image->src, FILEINFO_MIME_TYPE);

                $attachment = new TargetCheckAttachment();
                $attachment->target_check_id = $check->id;
                $attachment->name = basename($image->src);
                $attachment->type = $mimeType;
                $attachment->size = filesize($image->src);
                $attachment->path = hash('sha256', $image->src . rand() . time());
                $attachment->save();

                if (!@copy($image->src, Yii::app()->params['attachments']['path'] . '/' . $attachment->path)) {
                    $attachment->delete();
                }

                @unlink($image->src);
            }

            $imagePos = strpos($check->result, "<" . AttachedImage::TAG_MAIN);
        }
    }

    /**
     * Check starter.
     */
    private function _startCheck($checkId) {
        $check = TargetCheck::model()->with("check", "language", "target")->findByAttributes(array(
            "status" => TargetCheck::STATUS_IN_PROGRESS,
            "pid" => null,
            "id" => $checkId
        ));

        if (!$check) {
            return;
        }

        $target = $check->target;
        $language = $check->language;

        if (!$language) {
            $language = Language::model()->findByAttributes(array(
                'default' => true
            ));
        }

        Yii::app()->language = $language->code;

        $filesPath = Yii::app()->params['automation']['filesPath'];
        $scripts = $check->check->scripts;

        foreach ($scripts as $script) {
            if (!$check->result) {
                $check->result = '';
            } else {
                $check->result .= "\n";
            }

            $now = new DateTime();
            $package = $script->package;

            $data = Yii::t("app", "The {script} script was used within this check against {target} on {date} at {time}", array(
                "{script}" => $package->name,
                "{target}" => $check->override_target ? $check->override_target : $target->host,
                "{date}" => $now->format("d.m.Y"),
                "{time}" => $now->format("H:i:s"),
            ));

            $check->result .= "$data\n" . str_repeat("-", 16) . "\n";

            try {
                $check->pid = posix_getpgid(getmypid());
                $check->started = $now->format("Y-m-d H:i:s");
                $check->target_file = $this->_generateFileName();
                $check->result_file = $this->_generateFileName();
                $check->save();

                $inputFiles = $this->_createCheckFiles($check, $target, $script);

                $command = array(
                    "python",
                    "/opt/gtta/run_script.py",
                    $package->name,
                    "--pid=" . $check->pid,
                );

                $command[] = $filesPath . '/' . $check->target_file;
                $command[] = $filesPath . '/' . $check->result_file;

                foreach ($inputFiles as $input) {
                    $command[] = $filesPath . '/' . $input;
                }

                $vm = new VMManager();

                if ($vm->isRunning()) {
                    $output = $vm->runCommand(implode(" ", $command), false);
                    $fileOutput = file_get_contents($vm->virtualizePath($filesPath . '/' . $check->result_file));
                    $data = $fileOutput ? $fileOutput : $output;

                    $check->refresh();
                    $check->pid = null;
                    $check->result .= $data;

                    if (!$data) {
                        $check->result .= Yii::t('app', 'No output.');
                    }

                    $this->_getTables($check);
                    $this->_getImages($check);
                } else {
                    throw new VMNotFoundException(Yii::t("app", "Sandbox is not running, please regenerate it."));
                }

                $check->save();

                $started = new DateTime($check->started);
                $interval = time() - $started->getTimestamp();

                if ($interval > Yii::app()->params['automation']['minNotificationInterval']) {
                    $this->_sendNotification($check, $target);
                }
            } catch (VMNotFoundException $e) {
                $check->refresh();
                $check->pid = null;
                $check->result .= $e->getMessage();
            } catch (Exception $e) {
                $check->automationError($e->getMessage());
            }
        }

        $check->status = TargetCheck::STATUS_FINISHED;
        $check->save();
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        // start checks
        if (count($args) > 0) {
            if (count($args) != 1) {
                die("Invalid number of arguments.");
            }

            $checkId = (int) $args[0];

            if ($checkId) {
                $this->_startCheck($checkId);
            } else {
                die("Invalid arguments.");
            }

            exit();
        }

        // one instance check
        $fp = fopen(Yii::app()->params["automation"]["lockFile"], "w");
        
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            for ($i = 0; $i < 10; $i++) {
                $this->_system->refresh();

                if ($this->_system->status == System::STATUS_RUNNING) {
                    $this->_processStartingChecks();
                    $this->_processStoppingChecks();
                    $this->_processRunningChecks();

                    $this->_checkSystemIsRunning();
                }

                sleep(5);
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
