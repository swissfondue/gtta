<?php

/**
 * Class ModifiedPackagesJob
 */
class ModifiedPackagesJob extends BackgroundJob {
    /**
     * Job id
     */
    const ID_TEMPLATE = "gtta.package.modified.@obj_id@";

    /**
     * Update virtualized package's files
     * @param $package
     */
    private function _updatePackage($package) {
        $pm = new PackageManager();
        $vm = new VMManager();

        $path = $pm->getPath($package);
        $vPath = $vm->virtualizePath($path);

        FileManager::rmDir($vPath);
        FileManager::createDir($vPath, 0755, true);
        FileManager::copyRecursive($path, $vPath);

        $package->save();
    }
    /**
     * Perform
     */
    public function perform() {
        if (!isset($this->args['obj_id'])) {
            throw new Exception("Invalid job params.");
        }

        $id = $this->args['obj_id'];

        $package = Package::model()->findByPk($id);
        $this->_updatePackage($package);
    }
}