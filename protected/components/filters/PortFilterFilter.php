<?php

/**
 * Class PortFilterFilter
 */
class PortFilterFilter {
    const ID = "port-filter";
    const TITLE = "Port Filter";

    /**
     * Apply filter
     * @param $ports
     * @param $targets
     * @return array
     */
    public static function apply($ports, $targets) {
        $result = array();

        foreach ($targets as $target) {
            $targetData = explode(":", $target);

            if (count($targetData) == 1) {
                continue;
            }

            $port = (int) $targetData[1];

            if (in_array($port, $ports)) {
                $result[] = $target;
            }
        }

        return $result;
    }
}