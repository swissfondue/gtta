<?php
/**
 * Utils class
 */

class Utils {
    /**
     * Target masks
     */
    const TARGET_TYPE_MASK_DOMAIN     = "^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,24}$";
    const TARGET_TYPE_MASK_IP         = "(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)";
    const TARGET_TYPE_MASK_IP_NETWORK = "(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/(3[12]|2[0-9]{1}|[01]?[0-9])";

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
     * Check if string is smth like "domain.com"
     * @param $content
     * @return bool
     */
    public static function isDomain($content) {
        preg_match(sprintf("/%s/", self::TARGET_TYPE_MASK_DOMAIN), $content, $matched);

        if (empty($matched)) {
            return false;
        }

        return $matched[0] == $content;
    }

    /**
     * Check if string is IP
     * @param $content
     * @return bool
     */
    public static function isIP($content) {
        preg_match(sprintf("/%s/", self::TARGET_TYPE_MASK_IP), $content, $matched);

        if (empty($matched)) {
            return false;
        }

        return $matched[0] == $content;
    }

    /**
     * Check if string is IP range
     * @param $content
     * @return bool
     */
    public static function isIPRange($content) {
        preg_match(sprintf("/%s-%s/", self::TARGET_TYPE_MASK_IP, self::TARGET_TYPE_MASK_IP), $content, $matched);

        if (empty($matched)) {
            return false;
        }

        return $matched[0] == $content;
    }

    /**
     * Check if string is IP network
     * @param $content
     * @return bool
     */
    public static function isIPNetwork($content) {
        preg_match(sprintf("/%s/", self::TARGET_TYPE_MASK_IP_NETWORK), $content, $matched);

        if (empty($matched)) {
            return false;
        }

        return $matched[0] == $content;
    }
}
