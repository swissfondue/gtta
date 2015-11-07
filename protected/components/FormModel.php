<?php

/**
 * Form model class
 */
class FormModel extends CFormModel {
    /**
     * Fill attributes from model
     * @param ActiveRecord $model
     * @param array $preserve
     */
    public function fromModel(ActiveRecord $model, $preserve=array()) {
        $localNames = $this->attributeNames();

        foreach ($model->attributeNames() as $name) {
            $splitted = explode("_", $name);
            $localName = array_shift($splitted);
            $transformed = array();

            foreach ($splitted as $part) {
                $transformed[] = ucfirst($part);
            }

            $localName .= implode("", $transformed);

            if (in_array($localName, $preserve)) {
                continue;
            }

            if (in_array($localName, $localNames)) {
                $this->$localName = $model->$name;
            }
        }
    }
}
