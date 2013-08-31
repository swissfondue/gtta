<?php

/**
 * Base class for all application's commands.
 */
class ConsoleCommand extends CConsoleCommand {
    /**
     * @var $_system System
     */
    protected $_system = null;

    /**
     * Init a command object.
     */
    public function init() {
        $system = System::model()->findByPk(1);

        if (!$system->timezone) {
            $system->timezone = "Europe/Zurich";
        }

        date_default_timezone_set($system->timezone);
        $this->_system = $system;
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
     * @param $path
     */
    protected function _rmDir($path) {
        if (!is_dir($path)) {
            @unlink($path);
            return;
        }

        foreach (scandir($path) as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $this->_rmDir($path . "/" . $file);
        }

        @rmdir($path);
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
        if (!is_dir($source)) {
            return;
        }

        foreach (scandir($source) as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $srcPath = $source . "/" . $file;
            $dstPath = $destination . "/" . $file;

            $perms = @fileperms($srcPath);

            if ($perms === false) {
                continue;
            }

            if (is_dir($srcPath)) {
                $this->_createDir($dstPath, $perms);
                $this->_copyRecursive($srcPath, $dstPath);
            } else {
                $this->_copy($srcPath, $dstPath);
            }
        }
    }

    /**
     * Check system status
     */
    protected function _checkSystemIsRunning() {
        if ($this->_system->status != System::STATUS_RUNNING) {
            return;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('status', array(TargetCheck::STATUS_IN_PROGRESS, TargetCheck::STATUS_STOP));
        $checks = TargetCheck::model()->findAll($criteria);

        if (count($checks) > 0) {
            return;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('status', array(ProjectGtCheck::STATUS_IN_PROGRESS, ProjectGtCheck::STATUS_STOP));
        $checks = ProjectGtCheck::model()->findAll($criteria);

        if (count($checks) > 0) {
            return;
        }

        $this->_system->status = System::STATUS_IDLE;
        $this->_system->save();
    }
}
