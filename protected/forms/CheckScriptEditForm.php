<?php

/**
 * This is the model class for check script edit form.
 */
class CheckScriptEditForm extends LocalizedFormModel {
	/**
     * @var integer package id.
     */
    public $packageId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("packageId", "required"),
            array("packageId", "numerical", "integerOnly" => true),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			"packageId" => Yii::t("app", "Package"),
		);
	}

    /**
	 * Checks if script exists.
	 */
	public function checkClient($attribute, $params) {
		$package = Package::model()->findByAttributes(array(
            "id" => $this->packageId,
            "type" => Package::TYPE_SCRIPT,
            "status" => Package::STATUS_INSTALLED,
        ));

        if (!$package) {
            $this->addError("packageId", Yii::t("app", "Package not found."));
            return false;
        }

        return true;
	}
}