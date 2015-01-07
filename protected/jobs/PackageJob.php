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
        } catch (Exception $e) {
            $package->status = Package::STATUS_ERROR;
            $package->save();

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
     * Perform
     */
    public function perform() {
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
    }
}