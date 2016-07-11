<?php

/**
 * This is the model class for edit issue.
 * @property string $name
 * @property boolean $hidden
 */
class IssueEditForm extends CFormModel {
    /**
     * @var string title
     */
    public $name;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ["name", "required"],
            ["name", "length", "max" => 1000],
        ];
    }
}
