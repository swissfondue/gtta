<?php

/**
 * System controller.
 */
class SystemController extends Controller
{
    private $_databaseTables = array(
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
        'check_inputs',
        'check_inputs_l10n',
        'check_results',
        'check_results_l10n',
        'check_solutions',
        'check_solutions_l10n',
        'projects',
        'project_details',
        'targets',
        'target_references',
        'target_check_categories',
        'target_checks',
        'target_check_attachments',
        'target_check_inputs',
        'target_check_solutions',
    );

    private $_sequences = array(
        'check_categories_id_seq',
        'check_controls_id_seq',
        'check_inputs_id_seq',
        'check_results_id_seq',
        'check_solutions_id_seq',
        'checks_id_seq',
        'clients_id_seq',
        'emails_id_seq',
        'languages_id_seq',
        'project_details_id_seq',
        'projects_id_seq',
        'references_id_seq',
        'system_id_seq',
        'targets_id_seq',
        'users_id_seq',
    );

    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'checkAuth',
            'checkAdmin'
		);
	}

    /**
     * Backup file attachments.
     */
    private function _backupAttachments($attachmentsPath, $zip)
    {
        $attachments = TargetCheckAttachment::model()->findAll();

        foreach ($attachments as $attachment)
            if (file_exists(Yii::app()->params['attachments']['path'] . '/' . $attachment->path))
                $zip->addFile(
                    Yii::app()->params['attachments']['path'] . '/' . $attachment->path,
                    $attachmentsPath . '/' . $attachment->path
                );
    }

    /**
	 * Backup a table.
	 */
	private function _backupTable($table)
	{
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

        $dump[] = 'INSERT INTO ' . $db->quoteTableName($table) . '(' . implode(',', $columns) . ') VALUES' . implode(',', $values) . ';';

        return implode("\n", $dump) . "\n";
	}

    /**
     * Backup a sequence value.
     */
    private function _backupSequence($sequence)
    {
		$db  = Yii::app()->db;
		$pdo = $db->getPdoInstance();
        $row = $db->createCommand('SELECT last_value FROM ' . $db->quoteTableName($sequence) . ';')->queryRow();

        if (!$row)
            return '';

        $value = $row['last_value'];
        $dump  = 'SELECT setval(' . $pdo->quote($sequence) . ', ' . $pdo->quote($value) . ');';

        return $dump . "\n";
    }

    /**
     * Backup database.
     */
    private function _backupDatabase($dbPath, $zip)
    {
        $dumpPath = Yii::app()->params['tmpPath'] . '/' . hash('sha256', rand() . time());
        $dump     = fopen($dumpPath, 'w');

        // backup tables
        foreach ($this->_databaseTables as $table)
            fwrite($dump, $this->_backupTable($table));

        // backup sequences
        foreach ($this->_sequences as $sequence)
            fwrite($dump, $this->_backupSequence($sequence));

        fclose($dump);

        $zip->addFile($dumpPath, $dbPath . '/database.sql');
    }

    /**
     * Backup the system.
     */
    private function _backup()
    {
        $backupName = Yii::app()->name . ' ' . Yii::t('app', 'Backup') . ' ' . date('Ymd-Hi');
        $backupPath = Yii::app()->params['tmpPath'] . '/' . $backupName . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($backupPath, ZipArchive::CREATE) !== true)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Error creating archive.'));
            return;
        }

        $zip->addEmptyDir($backupName);
        $zip->addEmptyDir($backupName . '/attachments');

        $this->_backupAttachments($backupName . '/attachments', $zip);
        $this->_backupDatabase($backupName, $zip);

        $zip->close();

        $system = System::model()->findAll();

        if ($system)
            $system = $system[0];
        else
            $system = new System();

        $system->backup = new CDbExpression('NOW()');
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

        exit();
    }

    /**
     * Create a system backup.
     */
	public function actionBackup()
	{
        $criteria = new CDbCriteria();
        $criteria->addInCondition('status', array(
            TargetCheck::STATUS_IN_PROGRESS,
            TargetCheck::STATUS_STOP
        ));

        $runningChecks = TargetCheck::model()->count($criteria);

        if (isset($_POST['SystemBackupForm']))
        {
            if (!$runningChecks)
                $this->_backup();
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'System backup canceled.'));
        }

        $system   = System::model()->findAll();
        $backedUp = '';

        if ($system)
        {
            $backedUp = new DateTime($system[0]->backup);
            $backedUp = $backedUp->format('d.m.Y H:i');
        }
        else
            $backedUp = Yii::t('app', 'Never');

        $this->breadcrumbs[Yii::t('app', 'System Backup')] = '';

        // display the page
        $this->pageTitle = Yii::t('app', 'System Backup');
		$this->render('backup', array(
            'runningChecks' => $runningChecks,
            'backedUp'      => $backedUp
        ));
    }

    /**
     * Restore the attachments.
     */
    private function _restoreAttachments($zip, $attachments)
    {
        foreach ($attachments as $attachment)
        {
            $content = $zip->getFromIndex($attachment['index']);
            $name    = $attachment['name'];

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
        foreach (array_reverse($this->_databaseTables) as $table)
            $db->createCommand('TRUNCATE TABLE ' . $db->quoteTableName($table) . ' CASCADE;')->execute();

        // insert content
        $commands = explode("\n", $dbDump);

        foreach ($commands as $command)
        {
            if (!$command)
                continue;

            $db->createCommand($command)->execute();
        }

        $transaction->commit();
    }

    /**
     * Restore the system.
     */
    private function _restore($model)
    {
        $zip = new ZipArchive();

        if ($zip->open($model->backup->tempName) !== true)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Error opening backup file.'));
            return false;
        }

        $dbDump      = null;
        $attachments = array();

        // check if archive contains database dump
        for ($i = 0; $i < $zip->numFiles; $i++)
        {
            $stat = $zip->statIndex($i);

            $path = explode('/', $stat['name']);
            $name = $path[count($path) - 1];

            if ($name == 'database.sql')
            {
                $dbDump = $i;
                continue;
            }

            if ($path[1] == 'attachments' && count($path) == 3 && $path[2])
            {
                $attachments[] = array(
                    'index' => $i,
                    'name'  => $name
                );

                continue;
            }
        }

        if ($dbDump === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Invalid backup file.'));
            return false;
        }

        $this->_restoreAttachments($zip, $attachments);
        $this->_restoreDatabase($zip, $dbDump);

        $zip->close();

        return true;
    }

    /**
     * Restore the system from a backup.
     */
	public function actionRestore()
	{
        $criteria = new CDbCriteria();
        $criteria->addInCondition('status', array(
            TargetCheck::STATUS_IN_PROGRESS,
            TargetCheck::STATUS_STOP
        ));

        $runningChecks = TargetCheck::model()->count($criteria);
        $model         = new SystemRestoreForm();

        if (isset($_POST['SystemRestoreForm']))
        {
            $model->attributes = $_POST['SystemRestoreForm'];
            $model->backup     = CUploadedFile::getInstanceByName('SystemRestoreForm[backup]');

            if (!$runningChecks)
            {
                if ($model->validate())
                {
                    if ($this->_restore($model))
                        Yii::app()->user->setFlash('success', Yii::t('app', 'System successfully restored from the backup.'));
                }
                else
                    Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'System restore canceled.'));

        }

        $this->breadcrumbs[Yii::t('app', 'System Restore')] = '';

        // display the page
        $this->pageTitle = Yii::t('app', 'System Restore');
		$this->render('restore', array(
            'runningChecks' => $runningChecks,
            'model'         => $model
        ));
    }
}