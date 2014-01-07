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
     * @var float low risk pedestal rating
     */
    public $reportLowPedestal;

    /**
     * @var float med risk pedestal rating
     */
    public $reportMedPedestal;

    /**
     * @var float high risk pedestal rating
     */
    public $reportHighPedestal;

    /**
     * @var float max report rating
     */
    public $reportMaxRating;

    /**
     * @var med region damping factor for low risk checks
     */
    public $reportMedDampingLow;

    /**
     * @var high region damping factor for low risk checks
     */
    public $reportHighDampingLow;

    /**
     * @var high region damping factor for med risk checks
     */
    public $reportHighDampingMed;

    /**
     * @var string copyright text
     */
    public $copyright;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("timezone", "required"),
            array("timezone", "in", "range" => array_keys(TimeZones::$zones)),
            array("workstationId", "length", "is" => 36),
            array("workstationKey, copyright", "length", "max" => 1000),
            array("reportLowPedestal, reportMedPedestal, reportHighPedestal, reportMaxRating, reportMedDampingLow, reportHighDampingLow, reportHighDampingMed", "numerical", "min" => 0),
            array("reportLowPedestal", "compare", "compareAttribute" => "reportMedPedestal", "operator" => "<="),
            array("reportMedPedestal", "compare", "compareAttribute" => "reportHighPedestal", "operator" => "<="),
            array("reportHighPedestal", "compare", "compareAttribute" => "reportMaxRating", "operator" => "<="),
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
            "reportLowPedestal" => Yii::t("app", "Low Risk Pedestal"),
            "reportMedPedestal" => Yii::t("app", "Medium Risk Pedestal"),
            "reportHighPedestal" => Yii::t("app", "High Risk Pedestal"),
            "reportMaxRating" => Yii::t("app", "Maximum Rating"),
            "reportMedDampingLow" => Yii::t("app", "Medium Risk Region: Low Risks"),
            "reportHighDampingLow" => Yii::t("app", "High Risk Region: Low Risks"),
            "reportHighDampingMed" => Yii::t("app", "High Risk Region: Medium Risks"),
            "copyright" => Yii::t("app", "Copyright"),
		);
	}
}