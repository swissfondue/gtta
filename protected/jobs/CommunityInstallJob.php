<?php

/**
 * Class CommunityInstallJob
 */
class CommunityInstallJob extends BackgroundJob {
    /**
     * System flag
     */
    const SYSTEM = false;

    /**
     * Job id
     */
    const JOB_ID = null;

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
            $pm->create($package);
        }
    }

    /**
     * Install checks
     * @param $checks
     */
    private function _installChecks($checks) {
        $cm = new CheckManager();

        foreach ($checks as $check) {
            $cm->create($check);
        }
    }

    /**
     * Update
     */
    private function _install() {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        if ($system->pid !== null) {
            if (ProcessManager::isRunning($system->pid)) {
                return;
            }

            System::model()->updateByPk(1, array(
                "pid" => null,
            ));

            return;
        }

        System::model()->updateByPk(1, array(
            "pid" => posix_getpgid(getmypid()),
        ));
        $exception = null;

        try {
            $installCandidates = $this->_status($system->integration_key);
            $this->_installPackages($installCandidates->packages);
            $this->_installChecks($installCandidates->checks);
        } catch (Exception $e) {
            $exception = $e;
        }

        // "finally" block emulation
        try {
            $this->_finish($system->integration_key);
            System::model()->updateByPk(1, array(
                "pid" => null,
            ));
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
        $this->_install();
    }
}