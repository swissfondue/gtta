<?php

/**
 * GT Automation class.
 */
class GtautomationCommand extends ConsoleCommand {
    /**
     * Process starting checks.
     */
    private function _processStartingChecks() {
        $checks = ProjectGtCheck::model()->findAllByAttributes(array(
            'status' => ProjectGtCheck::STATUS_IN_PROGRESS,
            'pid' => null
        ));

        foreach ($checks as $check) {
            ProcessManager::backgroundExec(
                Yii::app()->params['yiicPath'] . '/yiic gtautomation ' . $check->project_id . ' ' . $check->gt_check_id
            );
        }
    }

    /**
     * Process stopping checks.
     */
    private function _processStoppingChecks() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('status = :status');
        $criteria->params = array('status' => ProjectGtCheck::STATUS_STOP);

        $checks = ProjectGtCheck::model()->findAll($criteria);

        foreach ($checks as $check) {
            $fileOutput = null;

            if ($check->pid) {
                $fileName = Yii::app()->params['automation']['tempPath'] . '/' . $check->result_file;
                ProcessManager::killProcess($check->pid);

                if (file_exists($fileName)) {
                    $fileOutput = file_get_contents($fileName);
                }
            }

            if (!$check->result) {
                $check->result = '';
            }

            $check->result .= $fileOutput ? $fileOutput : 'No output.';
            $check->status = ProjectGtCheck::STATUS_FINISHED;
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
        $criteria->addInCondition('status', array(ProjectGtCheck::STATUS_IN_PROGRESS, ProjectGtCheck::STATUS_STOP));

        $checks = ProjectGtCheck::model()->findAll($criteria);

        foreach ($checks as $check) {
            // if task died for some reason
            if (!ProcessManager::isRunning($check->pid)) {
                $check->pid = null;

                if (!$check->result) {
                    $check->result = 'No output.';
                }

                $check->status = ProjectGtCheck::STATUS_FINISHED;
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
    private function _createCheckFiles($check, $interpreter, $script) {
        $tempPath = Yii::app()->params['automation']['tempPath'];
        $scriptsPath = Yii::app()->params['automation']['scriptsPath'];

        // create target file
        $targetFile = fopen($tempPath . '/' . $check->target_file, 'w');

        // base data
        fwrite($targetFile, $check->target . "\n");
        fwrite($targetFile, $check->protocol . "\n");
        fwrite($targetFile, $check->port . "\n");
        fwrite($targetFile, $check->language->code . "\n");

        // directories
        fwrite($targetFile, $scriptsPath . "\n");
        fwrite($targetFile, $tempPath . "\n");
        fwrite($targetFile, $interpreter['path'] . "\n");
        fwrite($targetFile, $interpreter['basePath'] . "\n");

        fclose($targetFile);

        // create empty result file
        $resultFile = fopen($tempPath . '/' . $check->result_file, 'w');
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

            $inputFile = fopen($tempPath . '/' . $input->file, 'w');
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
            'status' => TargetCheck::STATUS_IN_PROGRESS,
            'pid' => null,
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

        $tempPath = Yii::app()->params['automation']['tempPath'];
        $scripts = $check->check->check->scripts;

        foreach ($scripts as $script) {
            if (!$check->result) {
                $check->result = '';
            } else {
                $check->result .= "\n";
            }

            $package = $script->package;
            $check->result .= $package->name . "\n" . str_repeat("-", strlen($package->name)) . "\n";

            try {
                $pm = new PackageManager();
                $entryPoint = $pm->getEntryPoint($package);
                $interpreter = $pm->getInterpreter($package);

                foreach ($interpreter["env"] as $env => $value) {
                    putenv("$env=$value");
                }

                $now = new DateTime();
                $check->pid = posix_getpgid(getmypid());
                $check->started = $now->format("Y-m-d H:i:s");
                $check->target_file = $this->_generateFileName();
                $check->result_file = $this->_generateFileName();
                $check->save();

                $inputFiles = $this->_createCheckFiles($check, $interpreter, $script);
                chdir($pm->getPath($package));

                $command = array(
                    $interpreter["path"]
                );

                foreach ($interpreter["params"] as $param) {
                    $command[] = $param;
                }

                $command[] = $entryPoint;
                $command[] = $tempPath . '/' . $check->target_file;
                $command[] = $tempPath . '/' . $check->result_file;

                foreach ($inputFiles as $input) {
                    $command[] = $tempPath . '/' . $input;
                }

                $command = implode(' ', $command);

                $output = array();
                exec($command . ' 2>&1', $output);

                $fileOutput = file_get_contents($tempPath . '/' . $check->result_file);
                $check->refresh();
                $check->pid = null;
                $check->result .= $fileOutput ? $fileOutput : implode("\n", $output);

                if (!$check->result) {
                    $check->result = Yii::t("app", "No output.");
                }

                $this->_getTables($check);
                $this->_getImages($check);
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
            if (count($args) != 2) {
                echo 'Invalid number of arguments.' . "\n";
                exit();
            }

            $projectId = (int) $args[0];
            $checkId = (int) $args[1];

            if ($projectId && $checkId) {
                $this->_startCheck($projectId, $checkId);
            } else {
                echo 'Invalid arguments.' . "\n";
            }

            exit();
        }

        // one instance check
        $fp = fopen(Yii::app()->params['automation']['gtLockFile'], 'w');
        
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            for ($i = 0; $i < 10; $i++) {
                $this->_processStartingChecks();
                $this->_processStoppingChecks();
                $this->_processRunningChecks();

                $this->_checkSystemIsRunning();

                sleep(5);
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
