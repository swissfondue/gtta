<?php

/**
 * Stop backup on exit
 */
function stopBackupOnExit() {
    try {
        SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_BACKING_UP);
    } catch (Exception $e) {
        // ok, doesn't matter
    }
}

/**
 * Backup controller.
 */
class BackupController extends Controller {
    private $_tables = array(
        'languages',
        'clients',
        'users',
        'emails',
        'references',
        'check_categories',
        'check_categories_l10n',
        'check_controls',
        'check_controls_l10n',
        'checks',
        'checks_l10n',
        'packages',
        'package_dependencies',
        'check_scripts',
        'check_inputs',
        'check_inputs_l10n',
        'check_results',
        'check_results_l10n',
        'check_solutions',
        'check_solutions_l10n',
        'projects',
        'project_details',
        'project_users',
        'targets',
        'target_references',
        'target_check_categories',
        'target_checks',
        'target_check_attachments',
        'target_check_inputs',
        'target_check_solutions',
        'target_check_vulns',
        'target_custom_checks',
        'target_custom_check_attachments',
        'target_custom_check_vulns',
        'risk_templates',
        'risk_templates_l10n',
        'risk_categories',
        'risk_categories_l10n',
        'risk_category_checks',
        'report_templates',
        'report_templates_l10n',
        'report_template_summary',
        'report_template_summary_l10n',
        'report_template_sections',
        'report_template_sections_l10n',
        'gt_categories',
        'gt_categories_l10n',
        'gt_types',
        'gt_types_l10n',
        'gt_modules',
        'gt_modules_l10n',
        'gt_dependency_processors',
        'gt_checks',
        'gt_checks_l10n',
        'gt_check_dependencies',
        'project_gt_modules',
        'project_gt_checks',
        'project_gt_check_attachments',
        'project_gt_check_inputs',
        'project_gt_check_solutions',
        'project_gt_check_vulns',
        'project_gt_suggested_targets',
    );

    private $_sequences = array(
        'check_categories_id_seq',
        'check_controls_id_seq',
        'check_inputs_id_seq',
        'check_results_id_seq',
        'packages_id_seq',
        'check_scripts_id_seq',
        'check_solutions_id_seq',
        'checks_id_seq',
        'clients_id_seq',
        'emails_id_seq',
        'gt_categories_id_seq',
        'gt_check_dependencies_id_seq',
        'gt_checks_id_seq',
        'gt_dependency_processors_id_seq',
        'gt_modules_id_seq',
        'gt_types_id_seq',
        'languages_id_seq',
        'login_history_id_seq',
        'project_details_id_seq',
        'project_gt_suggested_targets_id_seq',
        'projects_id_seq',
        'references_id_seq',
        'report_template_sections_id_seq',
        'report_template_summary_id_seq',
        'report_templates_id_seq',
        'risk_categories_id_seq',
        'risk_templates_id_seq',
        'system_id_seq',
        'targets_id_seq',
        'users_id_seq',
    );

    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            'https',
			'checkAuth',
            'checkAdmin',
            "idle",
		);
	}

    /**
     * Backup file attachments.
     */
    private function _backupAttachments($attachmentsPath, $zip) {
        $attachments = array_merge(
            TargetCheckAttachment::model()->findAll(),
            TargetCustomCheckAttachment::model()->findAll()
        );

        foreach ($attachments as $attachment) {
            if (file_exists(Yii::app()->params['attachments']['path'] . '/' . $attachment->path)) {
                FileManager::zipFile(
                    $zip,
                    Yii::app()->params['attachments']['path'] . '/' . $attachment->path,
                    $attachmentsPath . '/' . $attachment->path
                );
            }
        }
    }

    /**
     * Backup user packages.
     */
    private function _backupPackages($packagesPath, $zip) {
        $packages = Package::model()->findAllByAttributes(array(
            "system" => false
        ));

        $pm = new PackageManager();

        foreach ($packages as $package) {
            $path = $pm->getPath($package);
            $zipPath = $packagesPath;

            if ($package->type == Package::TYPE_LIBRARY) {
                $zipPath .= "/lib";
            }

            $zipPath .= "/" . $package->name;
            FileManager::zipDirectory($zip, $path, $zipPath);
        }
    }

    /**
	 * Backup a table.
	 */
	private function _backupTable($table) {
        $dump = array();
		$db   = Yii::app()->db;
		$pdo  = $db->getPdoInstance();

		$rows = $db->createCommand('SELECT * FROM ' . $db->quoteTableName($table) . ';')->queryAll();

		if (!$rows)
			return '';

		$columns = array_map(array( $db, 'quoteColumnName' ), array_keys($rows[0]));
        $values  = array();

		foreach ($rows as $row)
		{
			foreach ($row as &$value)
            {
                if (is_bool($value))
                    $value = $value ? $pdo->quote($value) : "'0'";
                else
                    $value = ($value === null) ? 'NULL' : $pdo->quote($value);
            }

			$values[] = '(' . implode(',', $row) . ')';
		}

        $dump[] = '<gtta:sql>INSERT INTO ' . $db->quoteTableName($table) . '(' . implode(',', $columns) . ') VALUES' . implode(',', $values) . ';</gtta:sql>';

        return implode("", $dump) . "";
	}

    /**
     * Backup a sequence value.
     */
    private function _backupSequence($sequence) {
		$db  = Yii::app()->db;
		$pdo = $db->getPdoInstance();
        $row = $db->createCommand('SELECT last_value FROM ' . $db->quoteTableName($sequence) . ';')->queryRow();

        if (!$row)
            return '';

        $value = $row['last_value'];
        $dump  = '<gtta:sql>SELECT setval(' . $pdo->quote($sequence) . ', ' . $pdo->quote($value) . ');</gtta:sql>';

        return $dump;
    }

    /**
     * Backup database.
     */
    private function _backupDatabase($dbPath, $zip) {
        $dumpPath = Yii::app()->params['backups']['tmpFilesPath'] . '/' . hash('sha256', rand() . time());
        $dump     = fopen($dumpPath, 'w');

        // backup tables
        foreach ($this->_tables as $table)
            fwrite($dump, $this->_backupTable($table));

        // backup sequences
        foreach ($this->_sequences as $sequence)
            fwrite($dump, $this->_backupSequence($sequence));

        fclose($dump);

        FileManager::zipFile($zip, $dumpPath, $dbPath . '/database.xml');
    }

    /**
     * Backup the system.
     * @param $system System
     */
    private function _backup() {
        $exception = null;

        try {
            $backupName = Yii::app()->name . ' ' . Yii::t('app', 'Backup') . ' ' . date('Ymd-Hi');
            $fileName = md5($backupName . rand() . time());
            FileManager::createDir(Yii::app()->params['backups']['tmpFilesPath'], 0777);
            $backupPath = Yii::app()->params['backups']['tmpFilesPath'] . '/' . $fileName . '.zip';

            $zip = new ZipArchive();

            if ($zip->open($backupPath, ZipArchive::CREATE) !== true) {
                throw new Exception("Unable to create backup archive: $fileName");
            }

            $zip->addEmptyDir($backupName);
            $zip->addEmptyDir($backupName . '/attachments');
            $zip->addEmptyDir($backupName . '/packages');
            $zip->addEmptyDir($backupName . '/packages/lib');

            $this->_backupAttachments($backupName . '/attachments', $zip);
            $this->_backupPackages($backupName . '/packages', $zip);
            $this->_backupDatabase($backupName, $zip);

            $zip->close();

            $now = new DateTime();
            $system = System::model()->findByPk(1);
            $system->backup = $now->format("Y-m-d H:i:s");
            $system->save();

            // give user a file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $backupName . '.zip"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backupPath));

            ob_clean();
            flush();

            readfile($backupPath);
            FileManager::unlink($backupPath);
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "application");
            $exception = $e;
        }

        // "finally" block
        try {
            SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_BACKING_UP);
        } catch (Exception $e) {
            // swallow the exception
        }

        if ($exception !== null) {
            throw $exception;
        }

        exit();
    }

    /**
     * Create a system backup.
     */
	public function actionBackup() {
        $system = System::model()->findByPk(1);

        if (isset($_POST["BackupForm"])) {
            if ($this->_system->demo) {
                throw new CHttpException(403, Yii::t("app", "Backups are not available in the demo version."));
            }

            try {
                // just in case
                @ignore_user_abort(true);
                @register_shutdown_function("stopBackupOnExit");

                SystemManager::updateStatus(System::STATUS_BACKING_UP, System::STATUS_IDLE);
            } catch (Exception $e) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            try {
                $this->_backup();
            } catch (Exception $e) {
                throw new CHttpException(500, Yii::t("app", "Error creating backup."));
            }
        }

        if ($system->backup) {
            $backedUp = new DateTime($system->backup);
            $backedUp = $backedUp->format("d.m.Y H:i");
        } else {
            $backedUp = Yii::t("app", "Never");
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Backup"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Backup");
		$this->render("backup", array(
            "backedUp" => $backedUp
        ));
    }

    /**
     * Restore the attachments.
     */
    private function _restoreAttachments($zip, $attachments) {
        foreach ($attachments as $attachment) {
            $content = $zip->getFromIndex($attachment['index']);
            $file = fopen(Yii::app()->params['attachments']['path'] . '/' . $attachment['name'], 'wb');
            fwrite($file, $content);
            fclose($file);
        }
    }

    /**
     * Restore the database.
     */
    private function _restoreDatabase($zip, $dbDump)
    {
        $dbDump = $zip->getFromIndex($dbDump);
        $db     = Yii::app()->db;

        $transaction = $db->beginTransaction();

        // truncate all tables
        foreach (array_reverse($this->_tables) as $table)
            $db->createCommand('TRUNCATE TABLE ' . $db->quoteTableName($table) . ' CASCADE;')->execute();

        // insert content
        $commands = explode('</gtta:sql><gtta:sql>', $dbDump);

        foreach ($commands as $command)
        {
            $tagPosition = strpos($command, '<gtta:sql>');

            if ($tagPosition !== false)
                $command = substr($command, $tagPosition + strlen('<gtta:sql>'));

            $tagPosition = strpos($command, '</gtta:sql>');

            if ($tagPosition !== false)
                $command = substr($command, 0, $tagPosition);

            if (!$command)
                continue;

            $db->createCommand($command)->execute();
        }

        $transaction->commit();
    }

    /**
     * Restore the system.
     * @param $form RestoreForm
     */
    private function _restore($form) {
        $exception = null;

        try {
            $zip = new ZipArchive();

            if ($zip->open($form->backup->tempName) !== true) {
                throw new Exception("Unable to open archive");
            }

            $dbDump = null;
            $attachments = array();

            // check if archive contains database dump
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);

                $path = explode('/', $stat['name']);
                $name = $path[count($path) - 1];

                if ($name == 'database.xml') {
                    $dbDump = $i;
                    continue;
                }

                if ($path[1] == 'attachments' && count($path) == 3 && $path[2]) {
                    $attachments[] = array(
                        'index' => $i,
                        'name' => $name
                    );

                    continue;
                }
            }

            if ($dbDump === null) {
                throw new Exception("Invalid backup file");
            }

            $this->_restoreAttachments($zip, $attachments);
            $this->_restoreDatabase($zip, $dbDump);

            $zip->close();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "application");
            $exception = $e;
        }

        // "finally" block
        try {
            SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_RESTORING);
        } catch (Exception $e) {
            // swallow the exception
        }

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Restore the system from a backup.
     */
	public function actionRestore() {
        $form = new RestoreForm();

        if (isset($_POST["RestoreForm"])) {
            $form->attributes = $_POST["RestoreForm"];
            $form->backup = CUploadedFile::getInstanceByName("RestoreForm[backup]");

            if ($form->validate()) {
                if ($this->_system->demo) {
                    throw new CHttpException(403, Yii::t("app", "Backups are not available in the demo version."));
                }

                try {
                    @ignore_user_abort(true);
                    SystemManager::updateStatus(System::STATUS_RESTORING, System::STATUS_IDLE);
                } catch (Exception $e) {
                    throw new CHttpException(403, Yii::t("app", "Access denied."));
                }

                try {
                    $this->_restore($form);
                    Yii::app()->user->setFlash("success", Yii::t("app", "System successfully restored from the backup."));
                } catch (Exception $e) {
                    throw new CHttpException(500, Yii::t("app", "Error restoring backup."));
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Restore"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Restore");
		$this->render("restore", array(
            "model" => $form
        ));
    }
}