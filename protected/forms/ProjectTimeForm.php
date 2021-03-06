<?php

/**
 * This is the model class for project track time form.
 */
class ProjectTimeForm extends CFormModel {
    /**
     * @var float hours spent.
     */
    public $hoursSpent;

    /**
     * @var description of track time record
     */
    public $description;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array( "hoursSpent", "required" ),
            array( "hoursSpent", "numerical", "min" => 0 ),
            array( "description", "safe" )
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