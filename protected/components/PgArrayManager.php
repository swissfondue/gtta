<?php

/**
 * PgArrayManager class
 */
class PgArrayManager {
    /**
     * Convert PHP array to PostgreSQL array
     * @param $array
     * @return string
     */
    public static function pgArrayEncode($array) {
        $result = array();

        foreach ($array as $entry) {
            if (is_array($entry)) { // If supports nested arrays.
                $result[] = PgArrayManager::pgArrayEncode($entry);
            } else {
                $entry = str_replace('"', '\\"', $entry);
                $entry = pg_escape_string($entry);
                $result[] = '"' . $entry . '"';
            }
        }

	    return '{' . implode(',', $result) . '}';
    }

    /**
     * Convert PostgreSQL array PHP to array
     * @param $text
     * @return array
     */
    public static function pgArrayDecode($text) {
        if (is_null($text)) {
            return array();
        }

        if (!is_string($text) || $text == "{}") {
            return array();
        }

        $text = substr($text, 1, -1);// Removes starting "{" and ending "}"

        if (substr($text, 0, 1) == '"') {
            $text = substr($text, 1);
        }

        if (substr($text, -1, 1) == '"') {
            $text = substr($text, 0, -1);
        }

        if (strstr($text, '"')) { // Assuming string array.
            $values = explode('","', $text);
        } else { // Assuming Integer array.
            $values = explode(',', $text);
        }

        $fixed_values = array();

        foreach ($values as $value) {
            $value = str_replace('\\"', '"', $value);
            $fixed_values[] = $value;
        }

        return $fixed_values;
    }
}