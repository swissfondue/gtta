<?php

/**
 * Package command
 */
class PackageCommand extends ConsoleCommand {
    /**
     * Install packages
     */
    private function _installPackages() {
        $packages = Package::model()->findAllByAttributes(array(
            "status" => Package::STATUS_INSTALL
        ));

        $pm = new PackageManager();

        foreach ($packages as $package) {
            try{
                $pm->install($package);
            } catch (Exception $e) {
                $package->status = Package::STATUS_ERROR;
                $package->save();

                throw $e;
            }
        }
    }

    /**
     * Delete packages
     */
    private function _deletePackages() {
        $packages = Package::model()->findAllByAttributes(array(
            "status" => Package::STATUS_DELETE
        ));

        $pm = new PackageManager();

        foreach ($packages as $package) {
            try {
                $pm->delete($package);
            } catch (Exception $e) {
                $package->status = Package::STATUS_ERROR;
                $package->save();

                throw $e;
            }
        }
    }

    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        // one instance check
        $fp = fopen(Yii::app()->params["packages"]["lockFile"], "w");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            for ($i = 0; $i < 10; $i++) {
                $this->_system->refresh();

                if ($this->_system->status == System::STATUS_PACKAGE_MANAGER) {
                    try {
                        $this->_installPackages();
                        $this->_deletePackages();
                    } catch (Exception $e) {
                        Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                    }

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("status", array(Package::STATUS_INSTALL, Package::STATUS_DELETE));
                    $packages = Package::model()->findAll($criteria);

                    if (count($packages) == 0) {
                        try {
                            SystemManager::updateStatus(System::STATUS_IDLE);
                        } catch (Exception $e) {
                            // swallow exceptions
                        }
                    }
                }

                sleep(5);
            }

            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }
} 