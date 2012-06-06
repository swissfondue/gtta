<?php

/**
 * Size format class.
 */
class SizeFormat
{
    /**
     * Converts size into human-readable form.
     * @param integer $size size in bytes.
     * @return string human-friendly formatted size.
     */
    public static function format($size)
    {
        if ($size > 1024 * 1024)
            $size = sprintf('%.2f MB', $size / (1024 * 1024));
        else if ($size > 1024)
            $size = sprintf('%.2f KB', $size / 1024);
        else
            $size = $size . ' ' . Yii::t('app', 'byte|bytes', $size);

        return $size;
    }
}