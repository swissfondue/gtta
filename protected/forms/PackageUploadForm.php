<?php

/**
 * This is the model class for package upload form
 */
class PackageUploadForm extends CFormModel {
    /**
     * @var integer int.
     */
    public $type;

    /**
     * @var CUploadedFile file.
     */
    public $file;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("type, file", "required"),
            array("type", "in", "range" => array(Package::TYPE_LIBRARY, Package::TYPE_SCRIPT)),
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
            "type" => Yii::t("app", "Type"),
            "file" => Yii::t("app", "File"),
        );
	}
}