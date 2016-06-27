<?php

/**
 * This is the model class for edit issue.
 */
class IssueEditForm extends CFormModel {
    /**
     * @var string title
     */
    public $title;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ["email", "required"],
            ["email", "length", "max" => 1000]
        ];
    }
}