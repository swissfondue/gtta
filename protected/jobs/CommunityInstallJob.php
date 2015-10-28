<?php

/**
 * Class CommunityInstallJob
 */
class CommunityInstallJob extends BackgroundJob {
    /**
     * Get install candidates
     * @param $integrationKey
     * @return mixed
     */
    private function _status($integrationKey=null) {
        $args = array();

        if ($integrationKey) {
            $cm = new CheckManager();
            $pm = new PackageManager();
            $args = array(
                "checks" => $cm->getExternalIds(),
                "packages" => $pm->getExternalIds(),
            );
        }

        $api = new CommunityApiClient($integrationKey);

        return $api->status($args);
    }

    /**
     * Finish installation
     * @param $integrationKey
     */
    private function _finish($integrationKey=null) {
        $this->_status($integrationKey);

        $api = new CommunityApiClient($integrationKey);
        $api->finish();
    }

    /**
     * Install packages
     * @param $packages
     */
    private function _installPackages($packages, $initial=false) {
        $pm = new PackageManager();

        foreach ($packages as $package) {
            $pm->create($package, $initial);
        }
    }

    /**
     * Install checks
     * @param $checks
     */
    private function _installChecks($checks, $initial=false) {
        $cm = new CheckManager();

        foreach ($checks as $check) {
            $c = $cm->create($check, $initial);

            TargetCheckReindexJob::enqueue(array(
                "category_id" => $c->control->check_category_id
            ));
        }
    }

    /**
     * Update
     */
    private function _install() {
        /** @var System $system */
        $system = System::model()->findByPk(1);
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
        } catch (Exception $e) {
            // swallow exceptions
        }

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Install initial packages/checks
     * @throws Exception
     */
    private function _installInitial() {
        $exception = null;

        $installCandidates = $this->_status();
        $this->_installPackages($installCandidates->packages, true);
        $this->_installChecks($installCandidates->checks, true);
    }

    /**
     * Perform
     */
    public function perform() {
        $initial = isset($this->args["initial"]);

        try {
            if ($initial) {
                $this->_installInitial();
            } else {
                $this->_install();
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}