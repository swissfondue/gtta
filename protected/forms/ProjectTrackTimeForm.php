<?php

/**
 * This is the model class for project track time form.
 */
class ProjectTrackTimeForm extends CFormModel {
    /**
     * @var float hours spent.
     */
    public $hoursSpent;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("hoursSpent", "required"),
            array("hoursSpent", "numerical", "min" => 0),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "hoursSpent" => Yii::t("app", "Hours Spent"),
		);
	}
}