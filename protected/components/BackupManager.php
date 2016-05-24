<?php
/**
 * Class MatchVersionException
 */
class MatchVersionException extends Exception {};

/**
 * Class BackupManager
 */
class BackupManager {
    /**
     * Tables to backup
     * @var array
     */
    private $_tables = array(
        'languages',
        'clients',
        'users',
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
        'target_custom_checks',
        'target_custom_check_attachments',
        'risk_templates',
        'risk_templates_l10n',
        'risk_categories',
        'risk_categories_l10n',
        'risk_category_checks',
        'relation_templates',
        'relation_templates_l10n',
        'report_templates',
        'report_templates_l10n',
        'report_template_summary',
        'report_template_summary_l10n',
        'report_template_sections',
        'report_template_sections_l10n',
    );

    /**
     * Sequences to backup
     * @var array
     */
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
        'languages_id_seq',
        'login_history_id_seq',
        'project_details_id_seq',
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
     * Returns list of backup files
     */
    public function filesList() {
        $backupsDir = Yii::app()->params['backups']['path'];
        $files = glob($backupsDir . '/*.zip');

        // Sorting by date
        usort($files, function($a, $b) {
            return filectime($a) < filectime($b);
        });

        $backups = array_map(function ($file) {
            $created = new DateTime();
            $created->setTimestamp(filectime($file));

            return array(
                "filename" => basename($file),
                "created_at" => $created->format("d.m.Y H:i:s"),
            );
        }, $files);

        return $backups;
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
     * Backup user packages.
     */
    private function _backupPackages($packagesPath, $zip) {
        $packages = Package::model()->findAll();
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
     * Set version to backup
     * @param $zip
     * @param $dbPath
     */
    private function _setVersion($verPath, $zip) {
        $system = System::model()->findByPk(1);

        $dumpPath = Yii::app()->params['backups']['tmpFilesPath'] . '/' . hash('sha256', rand() . time());
        $dump     = fopen($dumpPath, 'w');

        fwrite($dump, "<version>" . $system->version . "</version>");
        fclose($dump);

        FileManager::zipFile($zip, $dumpPath, $verPath . '/version.xml');
    }

    /**
     * Check backup version matching
     * @param $zip
     * @param $verFile
     * @return bool
     */
    private function _checkVersion($zip, $verFile) {
        $system = System::model()->findByPk(1);
        $version = $zip->getFromIndex($verFile);
        $version = trim($version);
        $version = str_replace("<version>", "", $version);
        $version = str_replace("</version>", "", $version);

        return $version <= $system->version;
    }

    /**
     * Backup the system.
     */
    public function backup() {
        $backupName = Yii::app()->name . ' ' . Yii::t('app', 'Backup') . ' ' . date('Ymd-Hi');
        $fileName = md5($backupName . rand() . time());
        FileManager::createDir(Yii::app()->params['backups']['tmpFilesPath'], 0777);
        $tmpBackupPath = Yii::app()->params['backups']['tmpFilesPath'] . '/' . $fileName . '.zip';
        $backupPath = Yii::app()->params['backups']['path'] . '/' . $fileName . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($tmpBackupPath, ZipArchive::CREATE) !== true) {
            throw new Exception("Unable to create backup archive: $fileName");
        }

        $zip->addEmptyDir($backupName);
        $zip->addEmptyDir($backupName . '/attachments');
        $zip->addEmptyDir($backupName . '/packages');
        $zip->addEmptyDir($backupName . '/packages/lib');

        $this->_backupAttachments($backupName . '/attachments', $zip);
        $this->_backupPackages($backupName . '/packages', $zip);
        $this->_backupDatabase($backupName, $zip);
        $this->_setVersion($backupName, $zip);

        $zip->close();

        $now = new DateTime();
        $system = System::model()->findByPk(1);
        $system->backup = $now->format(ISO_DATE_TIME);
        $system->save();

        FileManager::copy($tmpBackupPath, $backupPath);
        FileManager::unlink($tmpBackupPath);
    }

    /**
     * Restore the system.
     * @param $zipPath
     * @throws Exception
     * @throws null
     */
    public function restore($zipPath) {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new Exception("Unable to open archive");
        }

        $dbDump = null;
        $verFile = null;
        $attachments = array();

        // check if archive contains database dump & version file
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);

            $path = explode('/', $stat['name']);
            $name = $path[count($path) - 1];

            if ($name == 'database.xml') {
                $dbDump = $i;
                continue;
            }

            if ($name == 'version.xml') {
                $verFile = $i;
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

        if ($dbDump === null || $verFile === null) {
            throw new Exception("Invalid backup file");
        }

        if (!$this->_checkVersion($zip, $verFile)) {
            throw new MatchVersionException();
        }

        $this->_restoreAttachments($zip, $attachments);
        $this->_restoreDatabase($zip, $dbDump);
        $zip->close();

        FileManager::unlink($zipPath);
    }
}