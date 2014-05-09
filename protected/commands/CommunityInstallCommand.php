<?php

/**
 * Community install command
 */
class CommunityInstallCommand extends ConsoleCommand {
    /**
     * Get install candidates
     * @param $integrationKey
     * @return mixed
     */
    private function _status($integrationKey) {
        $cm = new CheckManager();
        $pm = new PackageManager();

        $api = new CommunityApiClient($integrationKey);
        return $api->status(array(
            "checks" => $cm->getExternalIds(),
            "packages" => $pm->getExternalIds(),
        ));
    }

    /**
     * Finish installation
     * @param $integrationKey
     */
    private function _finish($integrationKey) {
        $this->_status($integrationKey);

        $api = new CommunityApiClient($integrationKey);
        $api->finish();
    }

    /**
     * Install packages
     * @param $packages
     */
    private function _installPackages($packages) {
        $pm = new PackageManager();

        foreach ($packages as $package) {
            $pm->createPackage($package);
        }
    }

    /**
     * Install checks
     * @param $checks
     * @param $integrationKey
     */
    private function _installChecks($checks, $integrationKey) {
        $api = new CommunityApiClient($integrationKey);
        $catalogs = $api->getCatalogs();
        $cm = new CheckManager($catalogs);

        foreach ($checks as $check) {
            $cm->createCheck($check);
        }
    }

    /**
     * Update
     */
    private function _install() {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        if ($system->status != System::STATUS_COMMUNITY_INSTALL) {
            return;
        }

        if ($system->pid !== null) {
            if (ProcessManager::isRunning($system->pid)) {
                return;
            }

            SystemManager::updateStatus(System::STATUS_IDLE);

            return;
        }

        $system->pid = posix_getpgid(getmypid());
        $system->save();
        $exception = null;

        try {
            $installCandidates = $this->_status($system->integration_key);
            $this->_installPackages($installCandidates->packages);
            $this->_installChecks($installCandidates->checks, $system->integration_key);
        } catch (Exception $e) {
            $exception = $e;
        }

        // "finally" block emulation
        try {
            $this->_finish($system->integration_key);
            SystemManager::updateStatus(System::STATUS_IDLE);
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
        $fp = fopen(Yii::app()->params["community"]["install"]["lockFile"], "w");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            try {
                $this->_install();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
