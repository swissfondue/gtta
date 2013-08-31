<?php

/**
 * This is the model class for settings edit form.
 */
class SettingsEditForm extends CFormModel {
    /**
     * @var string workstation id
     */
    public $workstationId;

    /**
     * @var string workstation key
     */
    public $workstationKey;

    /**
     * @var string timezone
     */
    public $timezone;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("workstationId, workstationKey, timezone", "required"),
            array("timezone", "in", "range" => array_keys(TimeZones::$zones)),
            array("workstationId", "length", "is" => 36),
            array("workstationKey", "length", "max" => 1000),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "workstationId" => Yii::t("app", "Workstation ID"),
            "workstationKey" => Yii::t("app", "Workstation Key"),
			"timezone" => Yii::t("app", "Time Zone"),
		);
	}
}