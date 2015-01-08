<?php

/**
 * Base class for all application's commands.
 */
class ConsoleCommand extends CConsoleCommand {
    /**
     * @var $_system System
     */
    protected $_system = null;
    private $_lockFile = null;
    private $_fileHandle = null;

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
        $this->_lockFile = $this->_lockFileName();
    }

    /**
     * Returns lock file's name
     * @return mixed|string
     */
    private function _lockFileName() {
        $className = get_called_class();

        $fileName = str_replace('Command', '', $className);
        $fileName = preg_replace("/(?<=\\w)(?=[A-Z])/", "-$1", $fileName);
        $fileName = Yii::app()->params['tmpPath'] . '/' . Yii::app()->name . '.' . $fileName;
        $fileName = strtolower($fileName);

        return $fileName;
    }

    /**
     * Lock command lock file
     * @return bool
     */
    protected function lock() {
        $this->_fileHandle = fopen($this->_lockFile, "w");
        return flock($this->_fileHandle, LOCK_EX | LOCK_NB);
    }

    /**
     * Unlock command lock file
     */
    protected function unlock() {
        if ($this->_fileHandle) {
            flock($this->_fileHandle, LOCK_UN);
            fclose($this->_fileHandle);
        }
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
     * Run unlocked
     */
    protected function runUnlocked($args) {}

    /**
     * Run locked
     */
    protected function runLocked($args) {}

    /**
     * Locks and executes the command
     * @param array $args
     */
    public function run($args) {
        $this->runUnlocked($args);

        if ($this->lock()) {
            try {
                $this->runLocked($args);
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
            }

            $this->unlock();
        }
    }
}
