<?php

/**
 * Custom e-mail validator
 */
class EmailValidator extends CEmailValidator
{
    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        $value = str_replace(' ', '', $value);

        $object->$attribute = $value;

        parent::validateAttribute($object, $attribute);
    }
}
