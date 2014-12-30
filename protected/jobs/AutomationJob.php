<?php

/**
 * Class AutomationJob
 */
class AutomationJob extends BackgroundJob {
    /**
     * System flag
     */
    const SYSTEM = false;

    /**
     * Operation types
     */
    const OPERATION_START = "start";
    const OPERATION_STOP = "stop";

    /**
     * Automation job id template
     */
    const JOB_ID = "@app@.check.@operation@.@obj_id@";

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
     * Stop check
     * @param $id
     */
    private function _stopCheck($id) {
        $check = TargetCheck::model()->findByPk($id);

        if (!$check) {
            return;
        }

        $fileOutput = null;
        $job = JobManager::buildId($this::JOB_ID, array(
            "operation" => $this::OPERATION_START,
            "obj_id" => $check->id,
        ));
        $pid = JobManager::getPid($job);
        $vm = new VMManager();
        $vm->killProcessGroup($this->_translatePid($pid));

        sleep(5);

        $outFileName = $vm->virtualizePath(
            Yii::app()->params['automation']['filesPath'] . '/' . $check->result_file
        );

        if (file_exists($outFileName)) {
            $fileOutput = file_get_contents($outFileName);
        }

        if (!$check->result) {
            $check->result = '';
        }

        $check->result .= $fileOutput ? $fileOutput : 'No output.';
        $check->save();
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

            JobManager::enqueue(JobManager::JOB_EMAIL);
        }
    }

    /**
     * Create check files
     * @param $check
     * @param $target
     * @param $script
     * @return array
     * @throws VMNotFoundException
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
        $port = "";

        if ($target->port) {
            $port = $target->port;
        }

        if ($check->port) {
            $port = $check->port;
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
        $check = TargetCheck::model()->with("check", "language", "target")->findByPk($checkId);

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
        $scripts = $check->scriptsToStart;

        if (!count($scripts)) {
            $scripts = $check->check->scripts;
        }

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
                $pid = posix_getpgid(getmypid());

                $check->target_file = $this->_generateFileName();
                $check->result_file = $this->_generateFileName();
                $check->save();

                $inputFiles = $this->_createCheckFiles($check, $target, $script);

                $command = array(
                    "python",
                    "/opt/gtta/run_script.py",
                    $package->name,
                    "--pid=" . $pid,
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

                $started = TargetCheckManager::getStarted($check->id);

                if ($started) {
                    $started = new DateTime($started);
                    $interval = time() - $started->getTimestamp();

                    if ($interval > Yii::app()->params['automation']['minNotificationInterval']) {
                        $this->_sendNotification($check, $target);
                    }
                }
            } catch (VMNotFoundException $e) {
                $check->refresh();
                $check->result .= $e->getMessage();
            } catch (Exception $e) {
                $check->automationError($e->getMessage());
            }
        }

        $check->save();
    }

    /**
     * Perform job
     * @param $args
     */
    public function perform() {
        if (!isset($this->args["obj_id"]) || !isset($this->args["operation"])) {
            die("Invalid number of arguments.");
        }

        $operation = $this->args["operation"];
        $id = $this->args["obj_id"];

        switch ($operation) {
            case $this::OPERATION_START:
                $this->_startCheck($id);
                break;
            case $this::OPERATION_STOP:
                $this->_stopCheck($id);
                break;
            default:
                throw new Exception("Invalid operation.");
        }
    }
}