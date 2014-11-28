<?php

/**
 * Community share command
 */
class CommunityShareCommand extends ConsoleCommand {
    /**
     * Share references
     */
    private function _shareReferences() {
        $rm = new ReferenceManager();
        $references = Reference::model()->findAllByAttributes(array(
            "status" => Reference::STATUS_SHARE,
        ));

        foreach ($references as $reference) {
            $rm->share($reference);
        }
    }

    /**
     * Share categories
     */
    private function _shareCategories() {
        $cm = new CategoryManager();
        $categories = CheckCategory::model()->findAllByAttributes(array(
            "status" => CheckCategory::STATUS_SHARE,
        ));

        foreach ($categories as $category) {
            $cm->share($category);
        }
    }

    /**
     * Share controls
     */
    private function _shareControls() {
        $cm = new ControlManager();
        $controls = CheckControl::model()->findAllByAttributes(array(
            "status" => CheckControl::STATUS_SHARE,
        ));

        foreach ($controls as $control) {
            $cm->share($control);
        }
    }

    /**
     * Install packages
     */
    private function _sharePackages() {
        $pm = new PackageManager();
        $packages = Package::model()->with("dependencies")->findAllByAttributes(array(
            "status" => Package::STATUS_SHARE,
        ));

        foreach ($packages as $package) {
            foreach ($package->dependencies as $dep) {
                if ($dep->external_id) {
                    continue;
                }

                $pm->share($dep);
            }

            $pm->share($package);
        }
    }

    /**
     * Share checks
     */
    private function _shareChecks() {
        $cm = new CheckManager();
        $checks = Check::model()->findAllByAttributes(array(
            "status" => Check::STATUS_SHARE,
        ));

        foreach ($checks as $check) {
            $cm->share($check);
        }
    }

    /**
     * Share check preparations
     */
    private function _share() {
        $references = Reference::model()->countByAttributes(array(
            "status" => Reference::STATUS_SHARE,
        ));

        $categories = CheckCategory::model()->countByAttributes(array(
            "status" => CheckCategory::STATUS_SHARE,
        ));

        $controls = CheckControl::model()->countByAttributes(array(
            "status" => CheckControl::STATUS_SHARE,
        ));

        $packages = Package::model()->countByAttributes(array(
            "status" => Package::STATUS_SHARE,
        ));

        $checks = Check::model()->countByAttributes(array(
            "status" => Check::STATUS_SHARE,
        ));

        if (!$references && !$categories && !$controls && !$packages && !$checks) {
            return;
        }

        /** @var System $system */
        $system = System::model()->findByPk(1);

        SystemManager::updateStatus(System::STATUS_COMMUNITY_SHARE, array(
            System::STATUS_IDLE,
            System::STATUS_COMMUNITY_SHARE
        ));

        if ($system->pid !== null) {
            if (ProcessManager::isRunning($system->pid)) {
                return;
            }

            SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_COMMUNITY_SHARE);
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
            $this->_shareReferences();
            $this->_shareCategories();
            $this->_shareControls();
            $this->_sharePackages();
            $this->_shareChecks();
        } catch (Exception $e) {
            $exception = $e;
        }

        // "finally" block emulation
        try {
            SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_COMMUNITY_SHARE);
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
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $this->start();
    }

    /**
     * Execute
     */
    protected function exec() {
        try {
            $this->_share();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
        }
    }
}
