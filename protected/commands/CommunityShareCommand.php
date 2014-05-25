<?php

/**
 * Community share command
 */
class CommunityShareCommand extends ConsoleCommand {
    /**
     * Install packages
     */
    private function _sharePackages() {
        $pm = new PackageManager();
        $packages = Package::model()->with("dependencies")->findAllByAttributes(array(
            "status" => Package::STATUS_SHARE
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
            "status" => Check::STATUS_SHARE
        ));

        foreach ($checks as $check) {
            $cm->share($check);
        }
    }

    /**
     * Share check preparations
     */
    private function _share() {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        SystemManager::updateStatus(System::STATUS_COMMUNITY_SHARE, array(
            System::STATUS_IDLE,
            System::STATUS_COMMUNITY_SHARE
        ));

        if ($system->update_pid !== null) {
            if (ProcessManager::isRunning($system->update_pid)) {
                return;
            }

            SystemManager::updateStatus(System::STATUS_IDLE);
            $system->update_pid = null;
            $system->save();

            return;
        }

        $system->update_pid = posix_getpgid(getmypid());
        $system->save();
        $exception = null;

        try {
            $this->_sharePackages();
            $this->_shareChecks();
        } catch (Exception $e) {
            $exception = $e;
        }

        // "finally" block emulation
        try {
            SystemManager::updateStatus(System::STATUS_IDLE);
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
        $fp = fopen(Yii::app()->params["community"]["share"]["lockFile"], "w");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            try {
                $this->_share();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
