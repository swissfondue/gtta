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
     * @return null
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

    /**
     * Fill attributes from form
     * @param FormModel $form
     * @param array $preserve
     */
    public function fromForm(FormModel $form, $preserve=array()) {
        $localNames = $this->attributeNames();

        foreach ($form->attributeNames() as $name) {
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $name, $matches);
            $ret = $matches[0];

            foreach ($ret as &$match) {
                $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
            }

            $localName = implode("_", $ret);

            if (in_array($localName, $preserve)) {
                continue;
            }

            if (in_array($localName, $localNames)) {
                $value = $form->$name;

                if ($value === "") {
                    $value = null;
                }

                $this->$localName = $value;
            }
        }
    }
}
