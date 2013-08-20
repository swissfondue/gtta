<?php

/**
 * Base class for all application's commands.
 */
class ConsoleCommand extends CConsoleCommand {
    /**
     * Init a command object.
     */
    public function init() {
        $system = System::model()->findByPk(1);

        if (!$system->timezone) {
            $system->timezone = "Europe/Zurich";
        }

        date_default_timezone_set($system->timezone);
    }

    /**
     * Renders a template.
     */
    protected function render($template, $data = array()) {
        $path = Yii::getPathOfAlias($template).'.php';

        if (!file_exists($path))
            throw new Exception(Yii::t('app', 'Template {template} does not exist.', array(
                '{template}' => $path
            )));

        return $this->renderFile($path, $data, true);
    }

    /**
     * Check if process with given PID is running
     * @param $pid integer process id
     * @return boolean
     */
    protected function _isRunning($pid) {
        $data = shell_exec('ps ax -o  "%p %r" | grep ' . $pid);

        if (!$data) {
            return false;
        }

        $data = explode("\n", $data);

        if (count($data) >= 2) {
            return true;
        }

        return false;
    }

    /**
     * Kill process
     * @param $pid integer process id
     * @return boolean
     */
    protected function _killProcess($pid) {
        exec("kill -9 -" . $pid);
        return $this->_isRunning($pid);
    }

    /**
     * Run a background command
     * @param $cmd string command
     */
    protected function _backgroundExec($cmd) {
        exec($cmd . ' > /dev/null 2>&1 &');
    }

    /**
     * Run a command and check its return code
     * @param $cmd
     * @throws Exception
     */
    protected function _runCommand($cmd) {
        $output = null;
        $result = null;

        exec($cmd, $output, $result);

        if ($result !== 0) {
            throw new Exception("Invalid result code: $result");
        }
    }

    /**
     * Remove directory recursively
     * @param $dir
     */
    protected function _rmDir($dir) {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var $item RecursiveDirectoryIterator */
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } elseif ($item->isFile() || $item->isLink()) {
                @unlink($item->getPathname());
            }
        }

        @rmdir($dir);
    }

    /**
     * Create directory
     * @param $dir
     * @param $perms
     * @throws Exception
     */
    protected function _createDir($dir, $perms) {
        if (!@mkdir($dir, $perms)) {
            throw new Exception("Error creating directory: $dir");
        }
    }

    /**
     * Change permissions
     * @param $path
     * @param $perms
     * @throws Exception
     */
    protected function _chmod($path, $perms) {
        if (!@chmod($path, $perms)) {
            throw new Exception("Error changing permissions: $path");
        }
    }

    /**
     * Copy file
     * @param $source
     * @param $destination
     * @throws Exception
     */
    protected function _copy($source, $destination) {
        if (!@copy($source, $destination)) {
            throw new Exception("Error copying file $source to $destination");
        }
    }

    /**
     * Unlink file
     * @param $path
     * @throws Exception
     */
    protected function _unlink($path) {
        if (!@unlink($path)) {
            throw new Exception("Error deleting file: $path");
        }
    }

    /**
     * Create symlink
     * @param $link
     * @param $target
     * @throws Exception
     */
    protected function _createSymlink($link, $target) {
        if (!@symlink($target, $link)) {
            throw new Exception("Error creating symlink: $link");
        }
    }

    /**
     * Copy files recursively
     * @param $source
     * @param $destination
     * @throws Exception
     */
    protected function _copyRecursive($source, $destination) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $path = $iterator->getSubPathname();
            $srcPath = $source . "/" . $path;
            $dstPath = $destination . "/" . $path;

            if ($item->isDir()) {
                $this->_createDir($dstPath, fileperms($srcPath));
            } else {
                $this->_copy($srcPath, $dstPath);
            }
        }
    }
}
