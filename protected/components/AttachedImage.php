<?php

/**
 * Attached image class.
 */
class AttachedImage
{
    /**
     * Tag names
     */
    const TAG_MAIN = 'gtta-image';

    /**
     * Attributes
     */
    const ATTR_SRC = 'src';

    /**
     * @var image source
     */
    public $src = null;

    /**
     * Parse
     */
    public function parse($content) {
        $image = new SimpleXMLElement($content);
        $this->src = $image[self::ATTR_SRC];
    }
}