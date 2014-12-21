<?php
/**
 * Class ModifiedPackages
 */
class ModifiedPackagesCommand extends ConsoleCommand {
    /**
     * Run locked
     * @param $args
     */
    protected function runLocked($args) {
        $packages = Package::model()->findAllByAttributes(array(
            'modified' => true
        ));

        $pm = new PackageManager();
        $vm = new VMManager();

        foreach ($packages as $package) {
            $path = $pm->getPath($package);
            $vPath = $vm->virtualizePath($path);

            FileManager::rmDir($vPath);
            FileManager::createDir($vPath, 0755, true);
            FileManager::copyRecursive($path, $vPath);

            $package->modified = false;
            $package->save();
        }
    }
}