<?php

defined("DS") or define("DS", DIRECTORY_SEPARATOR);

/**
 * Merge array
 * @param $a
 * @param $b
 * @return array|mixed
 */
function mergeArray($a, $b) {
    $args = func_get_args();
    $res = array_shift($args);

    while (!empty($args)) {
        $next = array_shift($args);

        foreach ($next as $k => $v) {
            if (is_integer($k)) {
                isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
            } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                $res[$k] = mergeArray($res[$k], $v);
            } else {
                $res[$k] = $v;
            }
        }
    }

    return $res;
}

/**
 * Load either global or local config file
 * @param $dir
 * @param $file
 * @return array|mixed
 */
function loadConfig($dir, $file) {
    if (strpos(".php", $file) >= 0) {
        $file = str_replace(".php", "", $file);
    }

    $local = $dir . DS . $file . "-local.php";
    $production = $dir . DS . $file . ".php";

    $config = require($production);
    $config_local = array();

    if (file_exists($local)) {
        $config_local = require($local);
    }

    $config = mergeArray($config, $config_local);

    return $config;
}

if (!function_exists("array_column")) {
    function array_column( array $input, $column_key, $index_key = null ) {
        $result = array();

        foreach ($input as $k => $v) {
            $result[$index_key ? $v[$index_key] : $k] = $v[$column_key];
        }

        return $result;
    }
}
