<?php

/**
 * This is the model class for import target form.
 */
class TargetImportForm extends CFormModel {
    /**
     * @var CUploadedFile uploaded file.
     */
    public $file;

    /**
     * @var string $type
     */
    public $type;

    /**
     * @var integer $mappingId
     */
    public $mappingId;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["type", "required"],
            [
                "file",
                "file",
                "maxFiles" => 1,
                "types" => ["txt", "csv", "nessus"],
            ],
            ["type", "in", "range" => array_keys(ImportManager::$types)],
            ["type", "checkType"],
            ["mappingId", "numerical", "integerOnly" => true],
            ["mapping", "checkMapping"],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return [
            "file" => "File",
            "type" => "Type",
            "mappingId" => "Mapping"
        ];
    }

    /**
	 * Checks if type and extension are valid.
	 */
	public function checkType($attribute, $params) {
        if (!$this->file) {
            return false;
        }

        $extension = end(explode(".", $this->file->name));

        if (
            isset(ImportManager::$types[$this->type]) &&
            isset(ImportManager::$types[$this->type]["ext"]) &&
            $extension != ImportManager::$types[$this->type]["ext"]
        ) {
            $this->addError("file", Yii::t("app", "Invalid file extension."));
            return false;
        }

        return true;
	}

    /**
     * Validate mapping id
     * @param $attribute
     * @param $params
     * @return bool
     */
	public function checkMapping($attribute, $params) {
	    if (!isset($this->{$attribute})) {
	        return true;
        }

	    $mapping = NessusMapping::model()->findByPk($this->{$attribute});

        if (!$mapping) {
            $this->addError($attribute, "Mapping not found.");
            return false;
        }

        return true;
    }
}