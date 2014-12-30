<?php

/**
 * Check update command
 */
class CheckUpdateCommand extends ConsoleCommand {
    const SETUP_COMPLETED_FLAG = "/opt/gtta/.setup-completed";

    /**
     * Register the demo version
     */
    private function _register() {
        $api = new ServerApiClient($this->_system->workstation_id, $this->_system->workstation_key);
        $result = $api->register($this->_system->version);

        if ($result->id && $result->key) {
            $this->_system->workstation_id = $result->id;
            $this->_system->workstation_key = $result->key;
        }

        $this->_system->demo = true;
        $this->_system->save();
        $this->_system->refresh();
    }

    /**
     * Check update
     */
    private function _checkUpdate() {
        // check if setup has been completed
        if (!file_exists(self::SETUP_COMPLETED_FLAG)) {
            return;
        }

        if (!$this->_system->workstation_id && !$this->_system->workstation_key) {
            $this->_register();
        }

        $api = new ServerApiClient($this->_system->workstation_id, $this->_system->workstation_key);
        $result = $api->setStatus($this->_system->version, $this->_system->integration_key);
        $this->_system->demo = $result->demo;

        if ($result->update !== null) {
            $this->_system->update_version = $result->update->version;
            $this->_system->update_description = $result->update->description;
            $this->_system->update_check_time = new CDbExpression("NOW()");
        }

        $this->_system->save();

        if ($result->communityInstall && !$this->_system->demo) {
            try {
                JobManager::enqueue(JobManager::JOB_COMMUNITY_INSTALL);
            } catch (Exception $e) {
                // pass
            }
        }
    }

    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        $this->_checkUpdate();
    }
}
