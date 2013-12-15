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
     * Check system status
     */
    protected function _checkSystemIsRunning() {
        $this->_system->refresh();

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

        SystemManager::updateStatus(System::STATUS_IDLE);
    }
}
