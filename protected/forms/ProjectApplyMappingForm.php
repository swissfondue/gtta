<?php

/**
 * Class ProjectApplyMappingForm
 */
class ProjectApplyMappingForm extends CFormModel {
    /**
     * @var integer $projectId
     */
    public $projectId;

    /**
     * @var integer $mappingId
     */
    public $mappingId;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ["projectId, mappingId", "required"],
            ["projectId, mappingId", "numerical", "integerOnly" => true],
        ];
    }
}