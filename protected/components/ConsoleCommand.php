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

        if (flock($this->_fileHandle, LOCK_EX | LOCK_NB)) {
            return true;
        }

        return false;
    }

    /**
     * Unlock command lock file
     */
    protected function unlock() {
        if ($this->_fileHandle) {
            flock($this->_fileHandle, LOCK_UN);
        }
    }

    /**
     * Close lock file stream
     */
    protected function closeLockHandle() {
        if ($this->_fileHandle) {
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
     * Check system status
     */
    protected function _checkSystemIsRunning() {
        $this->_system->refresh();

        if ($this->_system->status != System::STATUS_RUNNING) {
            return;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition("status", array(TargetCheck::STATUS_IN_PROGRESS, TargetCheck::STATUS_STOP));
        $checks = TargetCheck::model()->findAll($criteria);

        if (count($checks) > 0) {
            return;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition("status", array(ProjectGtCheck::STATUS_IN_PROGRESS, ProjectGtCheck::STATUS_STOP));
        $checks = ProjectGtCheck::model()->findAll($criteria);

        if (count($checks) > 0) {
            return;
        }

        SystemManager::updateStatus(System::STATUS_IDLE, System::STATUS_RUNNING);
    }

    /**
     * Locks and executes the command
     */
    protected function start() {
        if ($this->lock()) {
            $this->exec();
            $this->unlock();
        }

        $this->closeLockHandle();
    }
}
