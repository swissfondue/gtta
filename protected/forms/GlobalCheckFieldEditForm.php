<?php

/**
 * This is the model class for global check field edit form
 */
class GlobalCheckFieldEditForm extends LocalizedFormModel
{
    /**
     * @var integer id
     */
    public $id;

    /**
     * @var string name.
     */
    public $name;

    /**
     * @var integer type.
     */
    public $type;

    /**
     * @var boolean hidden.
     */
    public $hidden;

    /**
     * @var array localized items
     */
    public $localizedItems;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            ["id", "numerical", "integerOnly" => true],
            ["name, type", "required"],
            ["type", "in", "range" => array_keys(GlobalCheckField::$fieldTypes)],
            ["hidden", "boolean"],
            ["localizedItems", "checkLocalized"],
            ["name", "match", "pattern" => '/[a-zA-Z0-9]/'],
            ["name", "checkName"],
        );
    }

    /**
     * Check name
     * @param $attribute
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function checkName($attribute, $params) {
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $this->{$attribute})) {
            $this->addError($attribute, "Invalid field name.");

            return false;
        }

        $field = null;

        if ($this->id) {
            $field = GlobalCheckField::model()->findByPk($this->id);

            if (!$field) {
                throw new Exception("Field not found", 404);
            }

            if (in_array($field->name, GlobalCheckField::$system)) {
                if ($this->name != $field->name) {
                    $this->addError("name", "You cannot change system field's name.");

                    return false;
                }

                return true;
            }
        }

        $existing = GlobalCheckField::model()->findByAttributes([
            "name" => $this->{$attribute}
        ]);

        if (!$existing) {
            return true;
        }

        if ($existing) {
            if (!$field) {
                // if new record
                $this->addError($attribute, "Field with that name already exists.");

                return false;
            } else {
                if ($field->id != $existing->id) {
                    $this->addError($attribute, "Field with that name already exists.");
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check localized items
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkLocalized($attribute, $params) {
        foreach ($this->{$attribute} as $la) {
            if (isset($la["title"]) && $la["title"]) {
                return true;
            }
        }

        $this->addError("localizedItems", "Empty title.");

        return false;
    }
}