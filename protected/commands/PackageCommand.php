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
            try {
                $this->_installPackages();
                $this->_deletePackages();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            }

            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }
} 