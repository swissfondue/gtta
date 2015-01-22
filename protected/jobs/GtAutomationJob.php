<?php

/**
 * Class GtAutomationJob
 */
class GtAutomationJob extends BackgroundJob {
    /**
     * Job id
     */
    const ID_TEMPLATE = "gtta.gt_check.project.@proj_id@.check.@obj_id@.@operation@";

    /**
     * Operations
     */
    const OPERATION_START   = 'start';
    const OPERATION_STOP    = 'stop';

    /**
     * Translate pid into the sandbox PID
     * @param $pid
     * @return int|null process id
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
     * Generate a file name for automated checks.
     */
    private function _generateFileName() {
        $name = null;

        while (true) {
            $name = hash('sha256', rand() . time() . rand());

            $check = ProjectGtCheckInput::model()->findByAttributes(array(
                'file' => $name
            ));

            if ($check) {
                continue;
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('target_file = :file OR result_file = :file');
            $criteria->params = array('file' => $name);

            $check = ProjectGtCheck::model()->find($criteria);

            if ($check) {
                continue;
            }

            break;
        }

        return $name;
    }

    /**
     * Create check files
     * @param $check
     * @param $project
     * @param $interpreter
     * @param $script
     * @return array
     */
    private function _createCheckFiles($check, $script) {
        $vm = new VMManager();
        $filesPath = $vm->virtualizePath(Yii::app()->params["automation"]["filesPath"]);

        // create target file
        $targetFile = fopen($filesPath . '/' . $check->target_file, 'w');

        $targetHost = $check->target;
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
        $resultFile = fopen($filesPath . '/' . $check->result_file, 'w');
        fclose($resultFile);

        $inputs = CheckInput::model()->findAllByAttributes(array(
            'check_script_id' => $script->id
        ));

        $inputIds = array();

        foreach ($inputs as $input) {
            $inputIds[] = $input->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            'project_id' => $check->project_id,
            'gt_check_id' => $check->gt_check_id,
        ));

        $criteria->addInCondition('check_input_id', $inputIds);
        $criteria->order = 'input.sort_order ASC';

        // create input files
        $inputs = ProjectGtCheckInput::model()->with('input')->findAll($criteria);
        $inputFiles = array();

        foreach ($inputs as $input) {
            $input->file = $this->_generateFileName();
            $input->save();

            $value = '';

            if ($input->input->type == CheckInput::TYPE_FILE) {
                if ($input->value) {
                    $value = $input->input->getFileData();
                }
            } else {
                $value = $input->value;
            }

            $inputFile = fopen($filesPath . '/' . $input->file, 'w');
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

                $attachment = new ProjectGtCheckAttachment();
                $attachment->gt_check_id = $check->gt_check_id;
                $attachment->project_id = $check->project_id;
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
    private function _startCheck($projectId, $checkId) {
        $check = ProjectGtCheck::model()->with(array(
            'check' => array(
                'with' => array(
                    'processor'
                )
            ),
            'language'
        ))->findByAttributes(array(
                'project_id' => $projectId,
                'gt_check_id' => $checkId
            ));

        if (!$check) {
            return;
        }

        $language = $check->language;

        if (!$language) {
            $language = Language::model()->findByAttributes(array(
                'default' => true
            ));
        }

        Yii::app()->language = $language->code;

        $filesPath = Yii::app()->params['automation']['filesPath'];
        $scripts = $check->check->check->scripts;

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
                "{target}" => $check->target,
                "{date}" => $now->format("d.m.Y"),
                "{time}" => $now->format("H:i:s"),
            ));

            $check->result .= "$data\n" . str_repeat("-", 16) . "\n";

            try {
                $pid = posix_getpid();
                $check->target_file = $this->_generateFileName();
                $check->result_file = $this->_generateFileName();
                $check->save();

                $inputFiles = $this->_createCheckFiles($check, $script);

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
                        $check->result .= Yii::t("app", "No output.");
                    }

                    $this->_getTables($check);
                    $this->_getImages($check);
                } else {
                    $check->refresh();
                    $check->result .= Yii::t("app", "Sandbox is not running, please regenerate it.") . "\n";
                }

                $check->save();

                // process dependencies
                if ($check->result && $check->check->processor) {
                    $processor = $check->check->processor->name;

                    $classNameParts = explode("-", $processor);
                    $classNameParts[] = "processor";
                    $className = "";

                    foreach ($classNameParts as $part) {
                        $className .= ucfirst($part);
                    }

                    $processor = new $className();
                    $processor->process($check);
                }
            } catch (Exception $e) {
                $check->automationError($e->getMessage());
            }
        }

        $check->status = ProjectGtCheck::STATUS_FINISHED;
        $check->save();
    }

    /**
     * Process stopping checks.
     */
    private function _stopCheck($projectId, $checkId) {
        $check = ProjectGtCheck::model()->findByAttributes(array(
            'project_id' => $projectId,
            'gt_check_id' => $checkId
        ));

        if (!$check) {
            throw new Exception("Check not found.");
        }

        $fileOutput = null;
        $job = JobManager::buildId(GtAutomationJob::ID_TEMPLATE, array(
            "operation" => GtAutomationJob::OPERATION_START,
            "proj_id" => $projectId,
            "obj_id" => $checkId,
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
        $check->status = ProjectGtCheck::STATUS_FINISHED;
        $check->save();
    }

    /**
     * Perform
     */
    public function perform() {
        try {
            if (!isset($this->args['proj_id']) || !isset($this->args['obj_id']) || !isset($this->args['operation'])) {
                throw new Exception("Invalid job params.");
            }

            $projectId = $this->args["proj_id"];
            $checkId = $this->args["obj_id"];
            $operation = $this->args["operation"];

            switch ($operation) {
                case self::OPERATION_START:
                    if (!isset($this->args["started"])) {
                        throw new Exception("Start Time is not defined.");
                    }

                    $this->setVar("started", $this->args["started"]);
                    $this->_startCheck($projectId, $checkId);
                    break;
                case self::OPERATION_STOP:
                    $this->_stopCheck($projectId, $checkId);
                    break;
                default:
                    throw new Exception("Invalid operation.");
            }

            $this->_startCheck($projectId, $checkId);
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());

            throw $e;
        }
    }
}