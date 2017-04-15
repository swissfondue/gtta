<?php

/**
 * Class AutomationJob
 */
class AutomationJob extends BackgroundJob {
    /**
     * Operation types
     */
    const OPERATION_START = "start";
    const OPERATION_STOP = "stop";

    /**
     * Automation job id template
     */
    const ID_TEMPLATE = "gtta.check.@operation@.@obj_id@";

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
        $job = JobManager::buildId(self::ID_TEMPLATE, array(
            "operation" => self::OPERATION_START,
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

        if ($fileOutput) {
            $check->appendPoc($fileOutput, false);
        } else {
            $check->appendPoc(Yii::t("app", "No output."));
        }

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
            $subject = Yii::t('app', '{checkName} check has been finished', array(
                '{checkName}' => $check->check->localizedName
            ));

            $content = $this->render(
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

            EmailJob::enqueue(array(
                "user_id" => $user->id,
                "subject" => $subject,
                "content" => $content,
            ));
        }
    }

    /**
     * Create check files
     * @param TargetCheck $check
     * @param Target $target
     * @param CheckScript $script
     * @return array
     * @throws Exception
     */
    private function _createCheckFiles(TargetCheck $check, Target $target, CheckScript $script) {
        $vm = new VMManager();
        $filesPath = $vm->virtualizePath(Yii::app()->params["automation"]["filesPath"]);

        // create target file
        $targetFile = @fopen($filesPath . '/' . $check->target_file, 'w');

        if (!$targetFile) {
            throw new VMNotFoundException("Sandbox is not running, please regenerate it.");
        }

        $targetHosts = "";
        $port = "";

        if (!$check->overrideTarget) {
            $targetHosts = $target->host;

            if ($target->port) {
                $port = $target->port;
            }

            if ($check->port) {
                $port = $check->port;
            }
        } else {
            $targets = explode("\n", $check->overrideTarget);
            $filtered = array();

            foreach ($targets as $t) {
                $t = trim($t);

                if ($t) {
                    $filtered[] = $t;
                }
            }

            $targetHosts = implode(",", $filtered);
        }

        $targetCheckScript = TargetCheckScript::model()->findByAttributes(array(
            "target_check_id" => $check->id,
            "check_script_id" => $script->id
        ));

        if (!$targetCheckScript) {
            throw new Exception("No such script attached to target.");
        }

        $timeout = $targetCheckScript->timeout;

        if (!$timeout) {
            $timeout = $script->package->timeout;
        }

        // base data
        fwrite($targetFile, $targetHosts . "\n");
        fwrite($targetFile, $check->applicationProtocol . "\n");
        fwrite($targetFile, $port . "\n");
        fwrite($targetFile, $check->language->code . "\n");
        fwrite($targetFile, $timeout . "\n");
        fclose($targetFile);

        // create empty result file
        $resultFile = @fopen($filesPath . '/' . $check->result_file, 'w');

        if (!$resultFile) {
            throw new VMNotFoundException("Sandbox is not running, please regenerate it.");
        }

        fclose($resultFile);

        // will use this input ids list when collecting all TargetCheckInputs below
        $inputIds = array();

        // create input entries, if they do not exist
        foreach ($script->inputs as $input) {
            $inputIds[] = $input->id;

            if ($input->visible) {
                continue;
            }

            $exists = TargetCheckInput::model()->findByAttributes(array(
                "target_check_id" => $check->id,
                "check_input_id" => $input->id
            ));

            if ($exists) {
                continue;
            }

            $newInput = new TargetCheckInput();
            $newInput->target_check_id = $check->id;
            $newInput->check_input_id = $input->id;
            $newInput->value = $input->value;
            $newInput->save();
        }

        $inputs = CheckInput::model()->with(array(
            "targetInputs" => array(
                "alias" => "ti",
                "on" => "ti.target_check_id = :tci",
                "params" => array("tci" => $check->id),
            )
        ))->findAllByAttributes(array(
            "check_script_id" => $script->id,
            "visible" => true,
        ));

        /** @var CheckInput $input */
        foreach ($inputs as $input) {
            $exists = $input->targetInputs;

            if ($exists && $exists[0]) {
                continue;
            }

            $newInput = new TargetCheckInput();
            $newInput->target_check_id = $check->id;
            $newInput->check_input_id = $input->id;
            $newInput->value = $input->value;
            $newInput->save();
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("target_check_id" => $check->id));
        $criteria->addInCondition("check_input_id", $inputIds);
        $criteria->order = "input.sort_order ASC";

        // create input files
        $inputs = TargetCheckInput::model()->with("input")->findAll($criteria);
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
     * @param TargetCheck $check
     */
    private function _getTables(&$check) {
        $poc = $check->getPoc();
        $tablePos = strpos($poc, '<' . ResultTable::TAG_MAIN);

        if ($tablePos !== false) {
            if (!$check->table_result) {
                $check->table_result = "";
            }

            $check->table_result .= substr($poc, $tablePos);
            $poc = substr($poc, 0, $tablePos);
            $check->setFieldValue(GlobalCheckField::FIELD_POC, $poc);
        }
    }

    /**
     * Get images from response
     * @param TargetCheck $check
     */
    private function _getImages(&$check) {
        $poc = $check->getPoc();
        $imagePos = strpos($poc, "<" . AttachedImage::TAG_MAIN);

        while ($imagePos !== false) {
            $imageEndPos = strpos($poc, ">", $imagePos);

            if ($imageEndPos === false) {
                break;
            }

            $imageTag = substr($poc, $imagePos, $imageEndPos + 1 - $imagePos);
            $poc = substr($poc, 0, $imagePos) . substr($poc, $imageEndPos + 1);
            $check->setFieldValue(GlobalCheckField::FIELD_POC, $poc);

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

            $imagePos = strpos($poc, "<" . AttachedImage::TAG_MAIN);
        }
    }

    /**
     * Check starter.
     * @param int $checkId
     */
    private function _startCheck($checkId) {
        /** @var TargetCheck $check */
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
        $scripts = $check->startScripts;

        if (!count($scripts)) {
            $scripts = $check->check->scripts;
        }

        foreach ($scripts as $script) {
            if ($check->getPoc()) {
                $check->appendPoc("\n");
            }

            $now = new DateTime();
            $package = $script->package;

            if (!isset($this->args["chain"])) {
                $data = Yii::t("app", "The {script} script was used within this check against {target} on {date} at {time}", array(
                    "{script}" => $package->name,
                    "{target}" => $check->overrideTarget ? Yii::t("app", "multiple targets") : $target->host,
                    "{date}" => $now->format("d.m.Y"),
                    "{time}" => $now->format("H:i:s"),
                ));

                $check->appendPoc("$data\n" . str_repeat("-", 16) . "\n");
            }

            try {
                $pid = posix_getpid();

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
                    $check->appendPoc($data, false);

                    if (!$data) {
                        $check->appendPoc(Yii::t('app', 'No output.'));
                    }

                    $this->_getTables($check);
                    $this->_getImages($check);
                } else {
                    throw new VMNotFoundException(Yii::t("app", "Sandbox is not running, please regenerate it."));
                }

                $check->save();

                $started = TargetCheckManager::getStartTime($check->id);

                if ($started) {
                    $started = new DateTime($started);
                    $interval = time() - $started->getTimestamp();

                    if ($interval > Yii::app()->params['automation']['minNotificationInterval']) {
                        $this->_sendNotification($check, $target);
                    }
                }
            } catch (VMNotFoundException $e) {
                $check->appendPoc($e->getMessage());
            } catch (Exception $e) {
                $check->automationError($e->getMessage());
            }
        }

        $check->save();
    }

    /**
     * Perform job
     */
    public function perform() {
        try {
            if (!isset($this->args["obj_id"]) || !isset($this->args["operation"])) {
                throw new Exception("Invalid job params.");
            }

            $operation = $this->args["operation"];
            $id = $this->args["obj_id"];

            switch ($operation) {
                case self::OPERATION_START:
                    if (!isset($this->args["started"])) {
                        throw new Exception("Start Time is not defined.");
                    }

                    $this->setVar("started", $this->args["started"]);
                    $this->_startCheck($id);
                    break;

                case self::OPERATION_STOP:
                    $this->_stopCheck($id);
                    break;

                default:
                    throw new Exception("Invalid operation.");
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}