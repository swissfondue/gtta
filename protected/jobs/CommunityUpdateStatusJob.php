<?php

/**
 * Update package/check statuses on community
 */
class CommunityUpdateStatusJob extends BackgroundJob {
    /**
     * Template for job's id
     */
    const ID_TEMPLATE = "gtta.community.update";

    /**
     * Run job
     */
    public function perform() {
        try {
            /** @var System $system */
            $system = System::model()->findByPk(1);

            $cm = new CheckManager();
            $checks = $cm->getExternalIds();
            $pm = new PackageManager();
            $packages = $pm->getExternalIds();

            if (!$checks && !$packages) {
                throw new Exception("No external checks or packages to update.");
            }

            if (!$system->integration_key) {
                throw new Exception("No integration key.");
            }

            $api = new CommunityApiClient($system->integration_key);
            $api->status(array(
                "checks" => $checks,
                "packages" => $packages,
            ));
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}