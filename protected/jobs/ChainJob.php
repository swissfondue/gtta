<?php
/**
 * Class ChainJob
 */
class ChainJob extends BackgroundJob {
    const OPERATION_START = "start";
    const OPERATION_STOP  = "stop";

    /**
     * ChainJob id template
     */
    const ID_TEMPLATE = "gtta.target.@target_id@.chain.@operation@";
    const CHAIN_STATUS_TEMPLATE = "gtta.target.@target_id@.chain.status";
    const CHAIN_CELL_ID_TEMPLATE = "gtta.target.@target_id@.chain.cell";

    /**
     * Start check chain
     * @param $target
     * @param $cellId
     * @param $inputTargets
     * @throws Exception
     */
    private function _startChain($target, $cellId=null, $inputTargets=null) {
        try {
            $relations = new SimpleXMLElement($target->relations, LIBXML_NOERROR);
        } catch (Exception $e) {
            throw new Exception("Invalid relations.");
        }

        $resuming = false;

        $redisCellId = TargetManager::getChainLastCellId($target->id);

        if ($cellId) {
            if ($redisCellId == $cellId) {
                $resuming = true;
            }

            $cell = RelationManager::getCell($relations, $cellId);

            if (!$cell) {
                throw new Exception("No graph cell with id: $cellId.");
            }
        } else {
            $cell = RelationManager::getStartCheck($relations);

            if (!$cell) {
                throw new Exception("Start check is not defined.");
            }
        }

        $attributes = $cell->attributes();
        $cellId = (int) $attributes->id;
        $cellType = (string) $attributes->type;
        $cellOutput = null;
        $stopperCell = false;

        switch ($cellType) {
            case RelationManager::MX_GRAPH_CELL_TYPE_CHECK:
                $checkId = (int) $attributes->check_id;
                $stopperCell = (int) $attributes->stopped;
                $check = TargetCheck::model()->findByAttributes(array(
                    "check_id" => $checkId,
                    "target_id" => $target->id
                ));

                if (!$check) {
                    throw new Exception("Check not found.");
                }

                if (!$resuming) {
                    if ($inputTargets) {
                        $filtered = array();

                        foreach (explode("\n", $inputTargets) as $t) {
                            $t = trim($t);

                            if ($t) {
                                $filtered[] = $t;
                            }
                        }

                        $check->override_target = implode("\n", $filtered);
                        $check->save();
                    }

                    TargetManager::setChainLastCellId($target->id, $cellId);
                    TargetCheckManager::start($check->id, true);

                    sleep(5);

                    while ($check->isRunning) {
                        sleep(5);
                    }

                    $check->refresh();
                }

                $cellOutput = $check->result;

                break;

            case RelationManager::MX_GRAPH_CELL_TYPE_FILTER:
                $filterName = (string) $attributes->filter_name;
                $filterValues = (string) $attributes->filter_values;
                $cellOutput = RelationManager::applyFilter($filterName, $filterValues, $inputTargets);

                break;

            default:
                throw new Exception("Unknown type of cell.");
                break;
        }

        if ($stopperCell && !$resuming) {
            TargetManager::setChainStatus($target->id, Target::CHAIN_STATUS_STOPPED);
        } else {
            $edges = RelationManager::getCellConnections($relations, $cellId);

            foreach ($edges as $edge) {
                $targetCellId = (int) $edge->attributes()->target;

                $this->_startChain($target, $targetCellId, $cellOutput);
            }
        }
    }

    /**
     * Stop target's check chain
     * @param $id
     * @throws Exception
     */
    private function _stopTargetChain($id) {
        $targetId = (int) $id;

        $target = Target::model()->findByPk($targetId);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        try {
            $relations = new SimpleXMLElement($target->relations, LIBXML_NOERROR);
        } catch (Exception $e) {
            throw new Exception("Invalid target relations.");
        }

        $checkIds = RelationManager::getCheckIds($relations);

        $targetChecks = TargetCheck::model()->findAllByAttributes(array(
            "check_id" => $checkIds,
            "target_id" => $target->id
        ));

        foreach ($targetChecks as $tc) {
            TargetCheckManager::stop($tc->id);
        }

        if (isset($this->args['reset'])) {
            TargetManager::setChainStatus($target->id, Target::CHAIN_STATUS_IDLE);
            TargetManager::delChainLastCellId($target->id);
        } else {
            TargetManager::setChainStatus($target->id, Target::CHAIN_STATUS_INTERRUPTED);
        }
    }

    /**
     * Tear down
     */
    public function tearDown() {
        $target = Target::model()->findByPk($this->args['target_id']);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        $status = TargetManager::getChainStatus($target->id);

        switch ($status) {
            case Target::CHAIN_STATUS_ACTIVE:
            case Target::CHAIN_STATUS_IDLE:
                TargetManager::setChainStatus($target->id, Target::CHAIN_STATUS_IDLE);
                TargetManager::delChainLastCellId($target->id);

                if (isset($this->args['reset'])) {
                    $message = sprintf("Check chain of target '%s' reset.", $target->host);
                } else {
                    $message = sprintf("Check chain of target '%s' completed.", $target->host);
                }

                break;

            case Target::CHAIN_STATUS_STOPPED:
                $message = sprintf("Check chain of target '%s' paused.", $target->host);

                break;

            case Target::CHAIN_STATUS_INTERRUPTED:
                $message = sprintf("Check chain of target '%s' interrupted by user.", $target->host);

                break;
            default:
                throw new Exception("Unknows chain status.");
        }

        $this->setVar("message", $message);

        JobManager::delKey($this->id . '.pid');
        JobManager::delKey($this->id . '.token');
        JobManager::delKey($this->id);
    }

    /**
     * Start target's check chain
     * @param $id
     * @throws Exception
     */
    private function _startTargetChain($id) {
        $targetId = (int) $id;

        $target = Target::model()->findByPk($targetId);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        $status = TargetManager::getChainStatus($this->args['target_id']);

        switch ($status) {
            case Target::CHAIN_STATUS_IDLE:
            case Target::CHAIN_STATUS_INTERRUPTED:
                $this->setVar("message", sprintf("Check chain of target '%s' started.", $target->host));

                TargetManager::setChainStatus($target->id, Target::CHAIN_STATUS_ACTIVE);
                $this->_startChain($target);

                break;

            case Target::CHAIN_STATUS_STOPPED:
                $this->setVar("message", sprintf("Check chain of target '%s' continued.", $target->host));

                TargetManager::setChainStatus($target->id, Target::CHAIN_STATUS_ACTIVE);
                $cellId = TargetManager::getChainLastCellId($target->id);
                $this->_startChain($target, $cellId);

                break;

            case Target::CHAIN_STATUS_ACTIVE:
                throw new Exception("Permission denied.");

                break;
            default:
                throw new Exception("Unknown chain status.");
        }
    }

    /**
     * Run
     * @param array $args
     */
    public function perform() {
        try {
            if (!isset($this->args['target_id']) && !isset($this->args['operation'])) {
                throw new Exception("Invalid job params.");
            }

            $target = Target::model()->findByPk($this->args['target_id']);

            if (!$target) {
                throw new Exception("Target not found.");
            }

            switch ($this->args['operation']) {
                case self::OPERATION_START:
                    $this->_startTargetChain($target->id);

                    break;

                case self::OPERATION_STOP:
                    $this->_stopTargetChain($target->id);

                    break;

                default:
                    throw new Exception("Unknown operation.");
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}