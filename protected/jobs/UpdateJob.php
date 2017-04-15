<?php
/**
 * Class UpdateJob
 */
class UpdateJob extends BackgroundJob {
    /**
     * Update job id template
     */
    const ID_TEMPLATE = "gtta.updating";

    const GTTA_USER = "gtta";
    const GTTA_GROUP = "gtta";
    const EXTRACTED_DIRECTORY = "gtta";
    const ARCHIVE_FILE = "gtta.zip";
    const SIGNATURE_FILE = "gtta.sig";
    const WEB_DIRECTORY = "web";
    const TOOLS_DIRECTORY = "tools";
    const INSTALL_SCRIPTS_DIRECTORY = "install";
    const INSTALL_SCRIPT = "install.sh";
    const REVERT_SCRIPT = "revert.sh";
    const CRONTAB_FILE = "crontab.txt";
    const MAKE_CONFIG_SCRIPT = "make_config.py";

    /**
     * Download, check and unpack the update
     * @param $targetVersion string
     * @param $workstationId string
     * @param $workstationKey string
     * @throws Exception
     */
    private function _getUpdate($targetVersion, $workstationId, $workstationKey) {
        $updateDir = Yii::app()->params["update"]["directory"];

        $zipPath = $updateDir . "/" . self::ARCHIVE_FILE;
        $sigPath = $updateDir . "/" . self::SIGNATURE_FILE;

        $api = new ServerApiClient($workstationId, $workstationKey);
        $api->getUpdateArchive($zipPath);
        $api->getUpdateSignature($sigPath);

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

        FileManager::copyRecursive($srcDir . "/" . self::WEB_DIRECTORY, $dstDir . "/" . self::WEB_DIRECTORY);
        FileManager::copyRecursive($srcDir . "/" . self::TOOLS_DIRECTORY, $dstDir . "/" . self::TOOLS_DIRECTORY);

        $protectedDir = $dstDir . "/" . self::WEB_DIRECTORY . "/protected";
        FileManager::chmod($protectedDir . "/yiic", 0750);
        ProcessManager::runCommand(sprintf("chown -R %s:%s %s", self::GTTA_USER, self::GTTA_GROUP, $dstDir));

        // update configuration
        ProcessManager::runCommand(sprintf(
            "python %s %s %s/config/",
            implode("/", array(
                $srcDir,
                self::TOOLS_DIRECTORY,
                self::MAKE_CONFIG_SCRIPT
            )),
            $params["deployConfig"],
            $protectedDir
        ));
    }

    /**
     * Run install script
     * @param $targetVersion
     */
    private function _runInstallScript($targetVersion) {
        $scriptPath = implode("/", array(
            Yii::app()->params["update"]["directory"],
            self::EXTRACTED_DIRECTORY,
            self::INSTALL_SCRIPTS_DIRECTORY,
            self::INSTALL_SCRIPT
        ));

        if (!file_exists($scriptPath)) {
            return;
        }

        FileManager::chmod($scriptPath, 0750);
        ProcessManager::runCommand($scriptPath);
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
            ProcessManager::runCommand("./yiic migrate --interactive=0");
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
        FileManager::unlink($oldCrontab);

        if (file_exists($newCrontab)) {
            FileManager::copy($newCrontab, $oldCrontab);
        }
    }

    /**
     * Change link to the new version
     * @param $targetVersion
     */
    private function _changeLink($targetVersion) {
        $versionPath = Yii::app()->params["update"]["versions"] . "/" . $targetVersion;
        $versionLink = Yii::app()->params["update"]["currentVersionLink"];

        FileManager::unlink($versionLink);
        FileManager::createSymlink($versionLink, $versionPath);
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
     * Revert install script
     */
    private function _revertInstallScript() {
        $scriptPath = implode("/", array(
            Yii::app()->params["update"]["directory"],
            self::EXTRACTED_DIRECTORY,
            self::INSTALL_SCRIPTS_DIRECTORY,
            self::REVERT_SCRIPT
        ));

        if (!file_exists($scriptPath)) {
            return;
        }

        FileManager::chmod($scriptPath, 0750);
        ProcessManager::runCommand($scriptPath);
    }

    /**
     * Prepare everything for update
     * @param $targetVersion string
     */
    private function _setup($targetVersion) {
        $this->_cleanup($targetVersion, false);

        $updateDir = Yii::app()->params["update"]["directory"];
        FileManager::createDir($updateDir, 0777);

        $versionDir = Yii::app()->params["update"]["versions"] . "/" . $targetVersion;
        FileManager::createDir($versionDir, 0750);
        FileManager::createDir($versionDir . "/" . self::WEB_DIRECTORY, 0750);
        FileManager::createDir($versionDir . "/" . self::TOOLS_DIRECTORY, 0750);
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

        FileManager::rmDir($updateDir);

        if (!$finished) {
            FileManager::rmDir(Yii::app()->params["update"]["versions"] . "/" . $targetVersion);
        }
    }

    /**
     * Update
     */
    private function _update() {
        $system = System::model()->findByPk(1);

        $targetVersion = $system->update_version;

        if (!$targetVersion) {
            return;
        }

        $finished = false;
        $exception = null;

        try {
            $this->_setup($targetVersion);
            ProcessManager::runCommand("/etc/init.d/cron stop");

            try {
                $this->_getUpdate($targetVersion, $system->workstation_id, $system->workstation_key);
                $this->_copyFiles($targetVersion);
                $this->_runInstallScript($targetVersion);

                try {
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
                    $this->_revertInstallScript();
                    throw $e;
                }
            } catch (Exception $e) {
                $exception = $e;
            }

            // "finally" block emulation
            try {
                ProcessManager::runCommand("/etc/init.d/cron start");
            } catch (Exception $e) {
                // swallow exceptions
            }

            if ($exception) {
                throw $exception;
            }

            System::model()->updateByPk(1, array(
                "version" => $system->update_version,
                "version_description" => $system->update_description,
                "update_version" => null,
                "update_description" => null,
                "update_time" => new CDbExpression("NOW()"),
            ));
        } catch (Exception $e) {
            $exception = $e;
        }

        // "finally" block emulation
        try {
            $this->_cleanup($targetVersion, $finished);
            RegenerateJob::enqueue();
        } catch (Exception $e) {
            // swallow exceptions
        }

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Perform
     */
    public function perform() {
        try {
            $this->_update();
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}