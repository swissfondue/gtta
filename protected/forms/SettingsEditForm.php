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
     * @var float med region damping factor for low risk checks
     */
    public $reportMedDampingLow;

    /**
     * @var float high region damping factor for low risk checks
     */
    public $reportHighDampingLow;

    /**
     * @var float high region damping factor for med risk checks
     */
    public $reportHighDampingMed;

    /**
     * @var string copyright text
     */
    public $copyright;

    /**
     * @var integer language id
     */
    public $languageId;

    /**
     * @var float min rating
     */
    public $communityMinRating;

    /**
     * @var bool allow unverified checks/packages
     */
    public $communityAllowUnverified;

    /**
     * @var bool show POC in checklist
     */
    public $checklistPoc;

    /**
     * @var bool show links in checklist
     */
    public $checklistLinks;

    /**
     * @var string email
     */
    public $email;

    /**
     * @var string mail host
     */
    public $mailHost;

    /**
     * @var integer mail port
     */
    public $mailPort;

    /**
     * @var string mail username
     */
    public $mailUsername;

    /**
     * @var string mail password
     */
    public $mailPassword;

    /**
     * @var string mail encryption
     */
    public $mailEncryption;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("timezone, communityMinRating, reportLowPedestal, reportMedPedestal, reportHighPedestal, reportMaxRating, reportMedDampingLow, reportHighDampingLow, reportHighDampingMed", "required"),
            array("timezone", "in", "range" => array_keys(TimeZones::$zones)),
            array("workstationId", "length", "is" => 36),
            array("workstationKey, copyright", "length", "max" => 1000),
            array("communityMinRating", "numerical", "min" => 0, "max" => 5),
            array("communityAllowUnverified, checklistPoc, checklistLinks", "boolean"),
            array("reportLowPedestal, reportMedPedestal, reportHighPedestal, reportMaxRating, reportMedDampingLow, reportHighDampingLow, reportHighDampingMed", "numerical", "min" => 0),
            array("reportLowPedestal", "compare", "compareAttribute" => "reportMedPedestal", "operator" => "<="),
            array("reportMedPedestal", "compare", "compareAttribute" => "reportHighPedestal", "operator" => "<="),
            array("reportHighPedestal", "compare", "compareAttribute" => "reportMaxRating", "operator" => "<="),
            array("languageId", "checkLanguage"),
            array("email", "email"),
            array("mailPort", "numerical", "integerOnly" => true, "min" => 1, "max" => 65535),
            array("mailHost, mailUsername, mailPassword", "safe"),
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
            "languageId" => Yii::t("app", "Default Language"),
            "communityMinRating" => Yii::t("app", "Community Min Rating"),
            "communityAllowUnverified" => Yii::t("app", "Community Allow Unverified"),
            "checklistPoc" => Yii::t("app", "Checklist POC"),
            "checklistLinks" => Yii::t("app", "Checklist Links"),
            "email" => Yii::t("app", "Email"),
            "mailHost" => Yii::t("app", "Host"),
            "mailPort" => Yii::t("app", "Port"),
            "mailUsername" => Yii::t("app", "Username"),
            "mailPassword" => Yii::t("app", "Password"),
            "mailEncryption" => Yii::t("app", "Encryption"),
		);
	}

    /**
     * Checks if language exists.
     */
    public function checkLanguage($attribute, $params) {
        $language = Language::model()->findByPk($this->languageId);

        if (!$language) {
            $this->addError("languageId", Yii::t("app", "Language not found."));
            return false;
        }

        return true;
    }
}