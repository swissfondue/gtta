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
}
