<?php

/**
 * This is the model class for system logo upload form
 */
class SystemLogoUploadForm extends CFormModel {
	/**
     * @var CUploadedFile image.
     */
    public $image;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("image", "required"),
            array( 
                "image",
                "file",
                "maxSize" => Yii::app()->params["systemLogo"]["maxSize"],
                "maxFiles" => 1,
                "types" => array("jpg", "png", "gif"),
            ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "image" => Yii::t("app", "Logo"),
        );
	}
}