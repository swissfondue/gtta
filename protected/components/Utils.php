<?php
/**
 * Utils class
 */

class Utils {
    /**
     * Check if content is HTML
     * @param $content
     * @return bool
     */
    public static function isHtml($content) {
        $htmlTests = array(
            "<b>",
            "<em>",
            "<u>",
            "<ul>",
            "<ol>",
            "<br />",
            "<br>",
        );

        $isHtml = false;

        foreach ($htmlTests as $test) {
            if (mb_strpos($content, $test) !== false) {
                $isHtml = true;
                break;
            }
        }

        return $isHtml;
    }


    /**
     * Underscore to camel case
     * @param $input
     * @param string $separator
     * @return mixed
     */
    public static function camelize($input, $separator = '_') {
        return str_replace($separator, '', lcfirst(ucwords($input, $separator)));
    }

    /**
     * Get first words of a string
     * @param $string
     * @param $numberOfWords
     * @return string
     */
    public static function getFirstWords($string, $numberOfWords) {
        $pattern = '/^(?>\S+\s*){1,' . $numberOfWords . '}/';
        $data = $string;

        if (preg_match($pattern, $string, $match)) {
            $data = rtrim($match[0]);
        }

        if (mb_strlen($data) < mb_strlen($string)) {
            $data .= "...";
        }

        return $data;
    }

    /**
     * Check for chars specific for html in string
     * @param $string
     * @return bool
     */
    public static function containsSpecificHtmlSymbols($string) {
        return preg_match("/<[^<]+>/", $string, $m) != 0;
    }

}
