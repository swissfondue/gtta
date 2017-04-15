<?php

/**
 * Class HostResolveJob
 */
class HostResolveJob extends BackgroundJob {
    /**
     * Perform
     */
    public function perform() {
        try {
            if (isset($this->args["targets"])) {
                $ids = is_array($this->args["targets"]) ? $this->args["targets"] : [$this->args["targets"]];
                $targets = Target::model()->findAllByPk($ids);

                if (!count($targets)) {
                    throw new Exception("Targets not found.");
                }
            } else {
                $targets = Target::model()->findAll();
            }

            $tm = new TargetManager();

            foreach ($targets as $t) {
                $tm->resolveHost($t);
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}