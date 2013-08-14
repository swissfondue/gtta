<?php

/**
 * Date/time format class
 */
class DateTimeFormat {
    /**
     * Convert date/time to ISO format
     */
    public static function toISO($time) {
        $target = new DateTime($time);
        return $target->format("Y-m-d H:i:s");
    }
}