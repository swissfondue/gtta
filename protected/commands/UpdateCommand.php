<?php

/**
 * Update command
 */
class UpdateCommand extends ConsoleCommand {
    const GTTA_USER = "gtta";
    const GTTA_GROUP = "gtta";
    const EXTRACTED_DIRECTORY = "gtta";
    const ARCHIVE_FILE = "gtta.zip";
    const SIGNATURE_FILE = "gtta.sig";
    const WEB_DIRECTORY = "web";
    const SCRIPTS_DIRECTORY = "scripts";
    const CRONTAB_FILE = "gtta.crontab.txt";

    /**
     * Download, check and unpack the update
     * @param $targetVersion string
     * @param $workstationId string
     * @param $workstationKey string
     */
    private function _getUpdate($targetVersion, $workstationId, $workstationKey) {
        $updateDir = Yii::app()->params["update"]["directory"];

        $zipPath = $updateDir . "/" . self::ARCHIVE_FILE;
        $sigPath = $updateDir . "/" . self::SIGNATURE_FILE;

        $api = new ApiClient($workstationId, $workstationKey);
        $api->getUpdateArchive($targetVersion, $zipPath);
        $api->getUpdateSignature($targetVersion, $sigPath);

        if (!file_exists($zipPath) || !file_exists($sigPath)) {
            throw new Exception("Error downloading files");
        }

        OpenSSL::checkFileSignature($zipPath, $sigPath, Yii::app()->params["update"]["keyFile"]);

        // extract archive
        $zip = new ZipArchive();

        if (!$zip->open($zipPath)) {
            throw new Exception("Error opening ZIP archive: $zipPath");
        }

        if (!$zip->extractTo($updateDir)) {
            throw new Exception("Error extracting files to $updateDir");
        }

        $zip->close();
    }

    /**
     * Copy files and set permissions
     * @param $targetVersion
     */
    private function _copyFiles($targetVersion) {
        $params = Yii::app()->params["update"];
        $srcDir = $params["directory"] . "/" . self::EXTRACTED_DIRECTORY;
        $dstDir = $params["versions"] . "/" . $targetVersion;

        $this->_copyRecursive($srcDir . "/" . self::WEB_DIRECTORY, $dstDir . "/" . self::WEB_DIRECTORY);
        $this->_copyRecursive($srcDir . "/" . self::SCRIPTS_DIRECTORY, $dstDir . "/" . self::SCRIPTS_DIRECTORY);

        $protectedDir = $dstDir . "/" . self::WEB_DIRECTORY . "/protected";

        $this->_chmod($protectedDir . "/yiic", 0750);
        $this->_runCommand(sprintf("chown -R %s:%s %s", self::GTTA_USER, self::GTTA_GROUP, $dstDir));

        // update configuration
        $this->_runCommand(sprintf(
            "python %s %s %s/config/",
            $params["deployScript"],
            $params["deployConfig"],
            $protectedDir
        ));
    }

    /**
     * Migrate database
     * @param $targetVersion
     */
    private function _migrateDatabase($targetVersion) {
        $protectedDir = implode("/", array(
            Yii::app()->params["update"]["versions"],
            $targetVersion,
            self::WEB_DIRECTORY,
            "protected"
        ));

        $currentDir = getcwd();
        $exception = null;

        if (!@chdir($protectedDir)) {
            throw new Exception("Unable to change directory to: $protectedDir");
        }

        try {
            $this->_runCommand("./yiic migrate --interactive=0");
        } catch (Exception $e) {
            $exception = $e;
        }

        // finally
        @chdir($currentDir);

        if ($exception !== null) {
            throw $exception;
        }
    }

    /**
     * Run tests
     */
    private function _runTests() {
        // @TODO: implement _runTests
    }

    /**
     * Update crontab
     */
    private function _updateCrontab() {
        $newCrontab = implode("/", array(
            Yii::app()->params["update"]["directory"],
            self::EXTRACTED_DIRECTORY,
            self::CRONTAB_FILE
        ));

        $oldCrontab = "/etc/cron.d/gtta";

        if (file_exists($oldCrontab)) {
            $this->_unlink($oldCrontab);
        }

        if (file_exists($newCrontab)) {
            $this->_copy($newCrontab, $oldCrontab);
        }
    }

    /**
     * Change link to the new version
     * @param $targetVersion
     */
    private function _changeLink($targetVersion) {
        $versionPath = Yii::app()->params["update"]["versions"] . "/" . $targetVersion;
        $versionLink = Yii::app()->params["update"]["currentVersionLink"];

        if (file_exists($versionLink)) {
            $this->_unlink($versionLink);
        }

        $this->_createSymlink($versionLink, $versionPath);
    }

    /**
     * Delete previous versions except the latest one
     */
    private function _deletePreviousVersions() {
        // @TODO: implement _deletePreviousVersions
    }

    /**
     * Revert crontab
     */
    private function _revertCrontab() {
        // @TODO: implement _revertCrontab
    }

    /**
     * Revert database
     */
    private function _revertDatabase() {
        // @TODO: implement _revertDatabase
    }

    /**
     * Prepare everything for update
     * @param $targetVersion string
     */
    private function _setup($targetVersion) {
        $this->_cleanup($targetVersion, false);

        $updateDir = Yii::app()->params["update"]["directory"];
        $this->_createDir($updateDir, 0777);

        $versionDir = Yii::app()->params["update"]["versions"] . "/" . $targetVersion;
        $this->_createDir($versionDir, 0750);
        $this->_createDir($versionDir . "/" . self::WEB_DIRECTORY, 0750);
        $this->_createDir($versionDir . "/" . self::SCRIPTS_DIRECTORY, 0750);
    }

    /**
     * Clean up
     * @param $targetVersion string
     * @param $finished boolean
     */
    private function _cleanup($targetVersion, $finished) {
        $updateDir = Yii::app()->params["update"]["directory"];

        @unlink($updateDir . "/" . self::ARCHIVE_FILE);
        @unlink($updateDir . "/" . self::SIGNATURE_FILE);

        $this->_rmDir($updateDir);

        if (!$finished) {
            $this->_rmDir(Yii::app()->params["update"]["versions"] . "/" . $targetVersion);
        }
    }

    /**
     * Update
     */
    private function _update() {
        $system = System::model()->findByPk(1);

        if ($system->status != System::STATUS_UPDATING) {
            return;
        }

        if ($system->update_pid != null) {
            if ($this->_isRunning($system->update_pid)) {
                return;
            }

            $system->update_pid = null;
            $system->status = System::STATUS_IDLE;
            $system->save();

            return;
        }

        $system->update_pid = posix_getpgid(getmypid());
        $system->save();
        $targetVersion = $system->update_version;

        $finished = false;
        $exception = null;

        try {
            $this->_setup($targetVersion);
            $this->_runCommand("/etc/init.d/cron stop");

            try {
                $this->_getUpdate($targetVersion, $system->workstation_id, $system->workstation_key);
                $this->_copyFiles($targetVersion);
                $this->_migrateDatabase($targetVersion);

                try {
                    $this->_runTests();
                    $this->_updateCrontab();

                    try {
                        $this->_changeLink($targetVersion);
                        $this->_deletePreviousVersions();

                        $finished = true;
                    } catch (Exception $e) {
                        $this->_revertCrontab();
                        throw $e;
                    }
                } catch (Exception $e) {
                    $this->_revertDatabase();
                    throw $e;
                }
            } catch (Exception $e) {
                $exception = $e;
            }

            // "finally" block emulation
            try {
                $this->_runCommand("/etc/init.d/cron start");
            } catch (Exception $e) {
                // swallow exceptions
            }

            if ($exception) {
                throw $exception;
            }

            $system->version = $system->update_version;
            $system->version_description = $system->update_description;
            $system->update_version = null;
            $system->update_description = null;
            $system->update_time = new CDbExpression("NOW()");
        } catch (Exception $e) {
            $exception = $e;
        }

        // "finally" block emulation
        try {
            $this->_cleanup($targetVersion, $finished);

            $system->status = System::STATUS_IDLE;
            $system->update_pid = null;
            $system->save();
        } catch (Exception $e) {
            // swallow exceptions
        }

        if ($exception) {
            throw $exception;
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $fp = fopen(Yii::app()->params["update"]["lockFile"], "w");

        if (flock($fp, LOCK_EX)) {
            try {
                $this->_update();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
