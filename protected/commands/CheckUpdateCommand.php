<?php

/**
 * Check update command
 */
class CheckUpdateCommand extends ConsoleCommand {
    /**
     * Check update
     */
    private function _checkUpdate() {
        $system = System::model()->findByPk(1);

        $api = new ApiClient($system->workstation_id, $system->workstation_key);
        $result = $api->setStatus($system->version);

        if ($result->update !== null && $result->update->version != $system->update_version) {
            $system->update_version = $result->update->version;
            $system->update_description = $result->update->description;
            $system->update_check_time = new CDbExpression('NOW()');
            $system->save();
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $fp = fopen(Yii::app()->params['check-update']['lockFile'], "w");

        if (flock($fp, LOCK_EX)) {
            try {
                $this->_checkUpdate();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), "error");
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
