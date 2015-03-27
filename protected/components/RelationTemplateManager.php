<?php

class RelationTemplateManager {
    const MX_GRAPH_CELL_TYPE_CHECK  = 'check';
    const MX_GRAPH_CELL_TYPE_FILTER = 'filter';

    const PORT_FILTER = "port-filter";

    public static $filters = array(
        self::PORT_FILTER
    );

    /**
     * Returns start check
     * @param $node
     * @return bool
     */
    public static function getStartCheck($node) {
        $cell = $node->xpath("//*[@type='check' and @start_check='1']");

        if (!$cell[0]) {
            return false;
        }

        return $cell[0];
    }

    /**
     * Returns relation's check ids
     * @param $node
     * @return array
     */
    public static function getCheckIds($node) {
        $ids = array();
        $cells = $node->xpath("//*[@type='check']");

        foreach ($cells as $cell) {
            $attributes = $cell->attributes();

            $ids[] = (int) $attributes->check_id;
        }

        return $ids;
    }

    /**
     * Returns cell by id
     * @param $node
     * @param $id
     * @return bool
     */
    public static function getCell($node, $id) {
        $cell = $node->xpath("//*[@type and @id=$id]");

        if (!$cell[0]) {
            return false;
        }

        return $cell[0];
    }

    /**
     * Returns cell's connections
     * @param $node
     * @param $id
     * @return mixed
     */
    public static function getCellConnections($node, $id) {
        return $node->xpath("//*[@type='connection' and @source=$id]");
    }

    /**
     * Apply relation template filter to result
     * @param $filter
     * @param $values
     * @param $input
     * @return array|null
     * @throws Exception
     */
    public static function applyFilter($filter, $values, $input) {
        $result = null;

        switch ($filter) {
            case self::PORT_FILTER:
                $ports = explode(",", $values);

                foreach ($ports as $key => $value) {
                    $port = intval($value);

                    if (!$port) {
                        throw new Exception("Invalid filter values");
                    }

                    $ports[$key] = $port;
                }

                $targets = explode("\n", $input);
                $result = Utils::filterTargetsByPorts($ports, $targets);
                $result = implode("\n", $result);

                break;
            default:
                throw new Exception("Unknown filter.");

                break;
        }

        return $result;
    }
}