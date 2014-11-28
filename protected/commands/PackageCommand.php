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
        $this->start();
    }

    /**
     * Execute
     */
    protected function exec() {
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
                        SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_PACKAGE_MANAGER);
                    } catch (Exception $e) {
                        // swallow exceptions
                    }
                }
            }

            sleep(5);
        }
    }
} 