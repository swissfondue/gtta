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

        preg_match_all('/(?<=^\{|,)(([^,"{]*)|\s*"((?:[^"\\\\]|\\\\(?:.|[\d+|x[0-9a-f]+))*)"\s*)(,|(?<!^\{)(?=\}$))/i', $text, $matches, PREG_SET_ORDER);
        $values = array();

        foreach ($matches as $match) {
            if ($match[3] != '') {
                $values[] = stripcslashes($match[3]);
            } elseif (strtolower($match[2]) == 'null') {
                $values[] = null;
            } else {
                $values[] = $match[2];
            }
        }

        return $values;
    }
}