<?php

/**
 * Install package command (used for dev & testing purposes)
 */
class InstallPackageCommand extends ConsoleCommand {
    /**
     * Install all available packages, starting from core
     */
    private function _installPackages() {
        $libsPath = Yii::app()->params["packages"]["path"]["libraries"];
        $scriptsPath = Yii::app()->params["packages"]["path"]["scripts"];
        $libraries = FileManager::getDirectoryContents($libsPath);
        $scripts = FileManager::getDirectoryContents($scriptsPath);

        $this->_installPackage($libsPath . "/core");

        foreach ($libraries as $library) {
            // skip core
            if ($library == "core" || substr($library, 0, 1) == ".") {
                continue;
            }

            $this->_installPackage($libsPath . "/" . $library);
        }

        foreach ($scripts as $script) {
            // skip libraries directory
            if ($script == "lib" || substr($script, 0, 1) == ".") {
                continue;
            }

            $this->_installPackage($scriptsPath . "/" . $script);
        }
    }

    /**
     * Install a single package
     * @param $path
     * @throws Exception
     */
    private function _installPackage($path) {
        echo "[" . substr($path, strrpos($path, "/") + 1) . "]\n";

        try {
            $pm = new PackageManager();
            $pm->installFromPath($path);
            echo "OK\n";
        } catch (Exception $e) {
            echo "FAILED\n";
            throw $e;
        }
    }

    /**
     * Run
     * @param array $args
     */
    protected function runUnlocked($args) {
        if (count($args) > 1) {
            die("Invalid number of arguments.");
        }

        $package = null;

        if (count($args) == 1) {
            $this->_installPackage($args[0]);
            return;
        }

        $this->_installPackages();
    }
} 