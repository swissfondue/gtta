<?php

/**
 * This is the model class for package upload form
 */
class PackageUploadForm extends CFormModel {
    /**
     * @var CUploadedFile file.
     */
    public $file;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("file", "required"),
            array(
                "file",
                "file",
                "maxSize" => Yii::app()->params["packages"]["maxSize"],
                "maxFiles" => 1,
                "types" => array("zip"),
            ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "file" => Yii::t("app", "File"),
        );
	}
}