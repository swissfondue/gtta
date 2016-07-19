<?php

/**
 * Class FieldManager
 */
class FieldManager {
    /**
     * Validate field value
     * @param $type
     * @param $value
     * @return bool
     * @throws Exception
     */
    public static function validateField($type, $value) {
        switch ($type) {
            case GlobalCheckField::TYPE_CHECKBOX:
            case GlobalCheckField::TYPE_WYSIWYG_READONLY:
            case GlobalCheckField::TYPE_TEXTAREA:
            case GlobalCheckField::TYPE_TEXT:
                return true;
            case GlobalCheckField::TYPE_RADIO:
                $values = json_decode($value, true);

                if ($values === null) {
                    return false;
                }

                foreach ($values as $v) {
                    if (is_array($v)) {
                        return false;
                    }
                }

                return true;

            default:
                throw new Exception("Invalid field type.");
        }
    }
}
