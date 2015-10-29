<?php

/**
 * Class GitSyncJob
 */
class GitJob extends BackgroundJob {
    /**
     * Job id
     */
    const ID_TEMPLATE = "gtta.packages.sync";

    /**
     * Actions
     */
    const ACTION_INIT = 0;
    const ACTION_CONFIGURE = 1;
    const ACTION_SYNC = 2;

    /**
     * @var System system
     */
    private $_system;

    /**
     * Initialize git
     * @throws Exception
     */
    private function _init() {
        $this->_system->updateGitStatus(System::GIT_STATUS_INIT);

        $cmd = sprintf(
            "%s/%s %s",
            Yii::app()->params["packages"]["git"]["scripts"]["path"],
            Yii::app()->params["packages"]["git"]["scripts"]["init"],
            Yii::app()->params["packages"]["path"]["scripts"]
        );

        ProcessManager::runCommand($cmd, true);

        $this->_system->updateGitStatus(System::GIT_STATUS_IDLE);
    }

    /**
     * Configure git
     * @throws Exception
     */
    private function _configure() {
        if (!SystemManager::gitInited()) {
            $this->_init();
        }

        $this->_system->updateGitStatus(System::GIT_STATUS_CONFIG);
        $args = array(
            "--dir",
            Yii::app()->params["packages"]["path"]["scripts"],
        );

        if ($this->_system->git_proto == System::GIT_PROTO_HTTPS) {
            $args = array_merge($args, array(
                "--repo",
                $this->_buildUrl(),
            ));
        } elseif ($this->_system->git_proto == System::GIT_PROTO_SSH) {
            $args = array_merge($args, array(
                "--repo",
                $this->_system->git_url,
                "--key",
                Yii::app()->params["system"]["filesPath"] . DS . Yii::app()->params["packages"]["git"]["key"]
            ));
        }

        $cmd = sprintf(
            "%s/%s %s",
            Yii::app()->params["packages"]["git"]["scripts"]["path"],
            Yii::app()->params["packages"]["git"]["scripts"]["configure"],
            implode(" ", $args)
        );

        ProcessManager::runCommand($cmd, true);
        $this->_system->updateGitStatus(System::GIT_STATUS_IDLE);
    }

    /**
     * Build configured url for git
     */
    private function _buildUrl() {
        $url = $this->_system->git_url;

        if ($this->_system->git_proto == System::GIT_PROTO_HTTPS) {
            $parts = parse_url($this->_system->git_url);
            $url = sprintf(
                "%s://%s:%s@%s",
                $parts["scheme"],
                $this->_system->git_username,
                $this->_system->git_password,
                $parts["host"] . $parts["path"]
            );
        }

        return $url;
    }

    /**
     * Synchronization
     * @param $strategy
     * @throws Exception
     */
    private function _sync($strategy) {
        $system = System::model()->findByPk(1);

        if (!SystemManager::gitInited()) {
            $this->_init();
        }

        if ($system->gitConfigured) {
            $this->_configure();
        }

        $system->updateGitStatus(System::GIT_STATUS_SYNC);
        $args = array(
            "--dir",
            Yii::app()->params["packages"]["path"]["scripts"],
            "--strategy",
            $strategy,
        );

        if ($system->git_proto == System::GIT_PROTO_SSH) {
            $args = array_merge($args, array(
                "--key",
                Yii::app()->params["packages"]["git"]["key"]
            ));
        }
        $cmd = sprintf(
            "%s/%s %s",
            Yii::app()->params["packages"]["git"]["scripts"]["path"],
            Yii::app()->params["packages"]["git"]["scripts"]["sync"],
            implode(" ", $args)
        );

        ProcessManager::runCommand($cmd, true);
        $this->_updatePackages();
        $system->updateGitStatus(System::GIT_STATUS_IDLE);
    }

    /**
     * Update packages in VZ
     */
    private function _updatePackages() {
        $path = Yii::app()->params["packages"]["path"]["scripts"];
        $vm = new VMManager();
        $vPath = $vm->virtualizePath($path);

        if (!is_dir($vPath)) {
            FileManager::createDir($vPath, 0777, true);
        }

        FileManager::copyRecursive(
            $path,
            $vPath
        );
    }

    /**
     * Perform
     */
    public function perform() {
        $this->_system = System::model()->findByPk(1);

        try {
            $strategy = isset($this->args["strategy"]) ? $this->args["strategy"] : System::GIT_MERGE_STRATEGY_THEIRS;
            $this->_sync($strategy);
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
            $this->_system->updateGitStatus(System::GIT_STATUS_FAILED);
        }
    }
}