<?php

/**
 * This is the model class for report template edit form.
 */
class ReportTemplateEditForm extends LocalizedFormModel {
    /**
     * @var int type.
     */
    public $type;

	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string sections
     */
    public $sections;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("type, name", "required"),
            array("type", "in", "range" => ReportTemplate::getValidTypes()),
            array("name", "length", "max" => 1000),
            array("sections", "checkSections")
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "type" => Yii::t("app", "Type"),
			"name" => Yii::t("app", "Name"),
            "intro" => Yii::t("app", "Introduction"),
            "appendix" => Yii::t("app", "Appendix"),
            "vulnsIntro" => Yii::t("app", "Vulns Introduction"),
            "infoChecksIntro" => Yii::t("app", "Info Checks Introduction"),
            "securityLevelIntro" => Yii::t("app", "Security Level Introduction"),
            "vulnDistributionIntro" => Yii::t("app", "Vuln Distribution Introduction"),
            "reducedIntro" => Yii::t("app", "Reduced Vuln List Introduction"),
            "highDescription" => Yii::t("app", "High Risk Description"),
            "medDescription" => Yii::t("app", "Med Risk Description"),
            "lowDescription" => Yii::t("app", "Low Risk Description"),
            "noneDescription" => Yii::t("app", "No Test Done Description"),
            "noVulnDescription" => Yii::t("app", "No Vulnerability Description"),
            "infoDescription" => Yii::t("app", "Info Description"),
            "degreeIntro" => Yii::t("app", "Degree of Fulfillment Introduction"),
            "riskIntro" => Yii::t("app", "Risk Matrix Introduction"),
            "footer" => Yii::t("app", "Footer"),
		);
	}

    /**
     * Check sections
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkSections($attribute, $params) {
        return false;
    }
}