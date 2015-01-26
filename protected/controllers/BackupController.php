<?php

/**
 * Backup controller.
 */
class BackupController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
        $ajax = implode(",", array(
            "create",
            "check",
            "controlBackup",
        ));

		return array(
            'https',
			'checkAuth',
            'checkAdmin',
            "idle",
            "ajaxOnly + " . $ajax,
            "postOnly + " . $ajax,
		);
	}

    /**
     * Displays list of backups.
     */
	public function actionIndex() {
        $this->breadcrumbs[] = array(Yii::t("app", "Backup"), "");
        $system = System::model()->findByPk(1);
        $bm = new BackupManager();
        $backups = $bm->filesList();

        // display the page
        $this->pageTitle = Yii::t("app", "Backup");

		$this->render("index", array(
            "backups" => $backups,
            "backingup" => $system->isBackingUp,
            "restoring" => $system->isRestoring
        ));
    }

    /**
     * Starts backup job
     */
    public function actionCreate() {
        $response = new AjaxResponse();

        try {
            $system = System::model()->findByPk(1);

            if ($system->isBackingUp) {
                throw new CHttpException(403, "Access Denied. The system is backing up.");
            }

            BackupJob::enqueue();
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Check if system is backing up || restoring
     */
    public function actionCheck($action) {
        $response = new AjaxResponse();

        try {
            $system = System::model()->findByPk(1);

            switch ($action) {
                case "backup":
                    $response->addData("backingup", $system->isBackingUp);
                    break;
                case "restore":
                    $response->addData("restoring", $system->isRestoring);
                    $job = JobManager::buildId(RestoreJob::ID_TEMPLATE);

                    if ($message = JobManager::getVar($job, "message")) {
                        $response->addData("message", $message);
                        JobManager::delKey("$job.message");
                    }

                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control backup
     */
    public function actionControlBackup() {
        $response = new AjaxResponse();

        try {
            $form = new EntryControlForm();
            $form->attributes = $_POST["EntryControlForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $filename = $form->id . '.zip';
            $path = Yii::app()->params['backups']['path'] . DIRECTORY_SEPARATOR . $filename;

            if (!file_exists($path)) {
                throw new CHttpException(404, Yii::t("app", "Backup file not found."));
            }

            switch ($form->operation) {
                case "delete":
                    FileManager::unlink($path);
                    break;

                case "restore":
                    $system = System::model()->findByPk(1);

                    if ($system->isRestoring) {
                        throw new CHttpException(403, Yii::t("app", "Access denied. The system is restoring."));
                    }

                    $tmpPath = Yii::app()->params['tmpPath'] . DIRECTORY_SEPARATOR . hash('sha256', $path . rand() . time());
                    FileManager::copy($path, $tmpPath);

                    RestoreJob::enqueue(array(
                        "path" => $tmpPath
                    ));

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Download backup
     */
    public function actionDownload($filename) {
        $path = Yii::app()->params['backups']['path'] . DIRECTORY_SEPARATOR . $filename . '.zip';

        if (!file_exists($path)) {
            throw new Exception("Backup file not found.");
        }

        $created = new DateTime();
        $created->setTimestamp(filectime($path));

        $backupName = Yii::app()->name . ' ' . Yii::t('app', 'Backup') . ' ' . $created->format('Ymd-Hi');

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $backupName . '.zip"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));

        ob_clean();
        flush();

        readfile($path);
    }

    /**
     * Restore the system from a backup.
     */
	public function actionRestore() {
        $system = System::model()->findByPk(1);
        $restoring = $system->isRestoring;

        $form = new RestoreForm();

        if (isset($_POST["RestoreForm"])) {
            $form->attributes = $_POST["RestoreForm"];
            $form->backup = CUploadedFile::getInstanceByName("RestoreForm[backup]");

            if ($form->validate()) {
                if ($restoring) {
                    throw new CHttpException(403, Yii::t("app", "Access denied. The system is restoring."));
                }

                $path = Yii::app()->params['tmpPath'] . DIRECTORY_SEPARATOR . hash('sha256', $form->backup->tempName . rand() . time());
                FileManager::copy($form->backup->tempName, $path);

                RestoreJob::enqueue(array(
                    "path" => $path
                ));

                $restoring = true;
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Restore"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Restore");
		$this->render("restore", array(
            "model" => $form,
            "restoring" => $restoring
        ));
    }
}