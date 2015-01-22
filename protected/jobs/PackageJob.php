<?php

/**
 * Class PackageJob
 */
class PackageJob extends BackgroundJob {
    /**
     * Job id
     */
    const ID_TEMPLATE = "gtta.package.@operation@.@obj_id@";

    const OPERATION_INSTALL  = 'install';
    const OPERATION_DELETE   = 'delete';

    /**
     * Install package
     */
    private function _installPackage($id) {
        $package = Package::model()->findByPk($id);

        if (!$package) {
            throw new Exception("Package not found.");
        }

        $pm = new PackageManager();

        try {
            $pm->install($package);

            $this->setVar("message", "");
        } catch (Exception $e) {
            $package->status = Package::STATUS_ERROR;
            $package->save();

            $this->setVar("message", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Delete package
     */
    private function _deletePackage($id) {
        $package = Package::model()->findByPk($id);

        if (!$package) {
            throw new Exception("Package not found.");
        }

        $pm = new PackageManager();

        try {
            $pm->delete($package);
        } catch (Exception $e) {
            $package->status = Package::STATUS_ERROR;
            $package->save();

            throw $e;
        }
    }

    /**
     * Job tear down
     */
    public function tearDown() {
        JobManager::delKey($this->id . '.pid');
        JobManager::delKey($this->id . '.token');
        JobManager::delKey($this->id);
    }

    /**
     * Perform
     */
    public function perform() {
        try {
            if (!isset($this->args["operation"]) || !isset($this->args["obj_id"])) {
                throw new Exception("Invalid job params.");
            }

            $operation = $this->args["operation"];
            $id = $this->args["obj_id"];

            try {
                switch ($operation) {
                    case self::OPERATION_INSTALL:
                        $this->_installPackage($id);
                        break;
                    case self::OPERATION_DELETE:
                        $this->_deletePackage($id);
                        break;
                    default:
                        throw new Exception("Invalid operation.");
                }
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            }

            sleep(5);
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());

            throw $e;
        }
    }
}