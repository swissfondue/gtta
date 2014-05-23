<?php

/**
 * Community refresh catalogs
 */
class CommunityRefreshCommand extends ConsoleCommand {
    /**
     * Refresh catalogs
     */
    private function _refresh() {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        if ($system->status != System::STATUS_IDLE) {
            return;
        }

        $api = new CommunityApiClient($system->integration_key);
        $catalogs = $api->getCatalogs();
        $system->community_catalogs_cache = json_encode($catalogs);
        $system->save();
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args) {
        $fp = fopen(Yii::app()->params["community"]["refresh"]["lockFile"], "w");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            try {
                $this->_refresh();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
            }

            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}
