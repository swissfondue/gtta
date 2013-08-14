<?php

/**
 * This is the model class for incoming check edit form
 */
class IncomingCheckEditForm extends LocalizedFormModel {
    /**
     * @var integer control id.
     */
    public $controlId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("controlId", "required"),
            array("controlId", "checkControl"),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			"controlId" => Yii::t("app", "Control"),
		);
	}

    /**
	 * Checks if control exists.
	 */
	public function checkControl($attribute, $params) {
		$control = CheckControl::model()->findByPk($this->controlId);

        if (!$control) {
            $this->addError("controlId", Yii::t("app", "Control not found."));
            return false;
        }

        return true;
	}
}