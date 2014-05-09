<?php

/**
 * Custom active record class
 */
class ActiveRecord extends CActiveRecord {
    /**
     * Save record
     * @param bool $runValidation
     * @param null $attributes
     * @throws Exception
     */
    public function save($runValidation=true, $attributes=null) {
        $result = parent::save($runValidation, $attributes);

        if (!$result) {
            $text = array();

            foreach ($this->getErrors() as $attribute => $errors) {
                $text[] = $attribute . ": " . implode(", ", $errors);
            }

            throw new Exception("Error saving record: " . implode("; ", $text));
        }
    }
}
