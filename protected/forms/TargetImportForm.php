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
     * @var string type
     */
    public $type;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("file, type", "required"),
            array(
                "file",
                "file",
                "maxFiles" => 1,
                "types" => array("txt", "csv", "nessus"),
            ),
            array("type", "in", "range" => array_keys(ImportManager::$types)),
            array("type", "checkType"),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            "file" => "File",
            "type" => "Type"
        );
    }

    /**
	 * Checks if type and extension are valid.
	 */
	public function checkType($attribute, $params) {
        $extension = end(explode(".", $this->file->name));

        if ($extension != ImportManager::$types[$this->type]["ext"]) {
            $this->addError("file", Yii::t("app", "Invalid file extension."));
            return false;
        }

        return true;
	}
}