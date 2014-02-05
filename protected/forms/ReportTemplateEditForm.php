<?php

/**
 * This is the model class for report template edit form.
 */
class ReportTemplateEditForm extends LocalizedFormModel {
	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string intro.
     */
    public $intro;

    /**
     * @var string appendix.
     */
    public $appendix;

    /**
     * @var string vulns intro.
     */
    public $vulnsIntro;

    /**
     * @var string info checks intro.
     */
    public $infoChecksIntro;

    /**
     * @var string security level intro.
     */
    public $securityLevelIntro;

    /**
     * @var string vuln distribution intro.
     */
    public $vulnDistributionIntro;

    /**
     * @var string reduced intro.
     */
    public $reducedIntro;

    /**
     * @var string risk intro.
     */
    public $riskIntro;

    /**
     * @var string degree intro.
     */
    public $degreeIntro;

    /**
     * @var string high description.
     */
    public $highDescription;

    /**
     * @var string med description.
     */
    public $medDescription;

    /**
     * @var string low description.
     */
    public $lowDescription;
    
    /**
     * @var string none description.
     */
    public $noneDescription;
    
    /**
     * @var string no vuln description.
     */
    public $noVulnDescription;
    
    /**
     * @var string info description.
     */
    public $infoDescription;

    /**
     * @var string footer.
     */
    public $footer;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("name", "required"),
            array("name", "length", "max" => 1000),
            array("intro, appendix, localizedItems, vulnsIntro, infoChecksIntro, securityLevelIntro, vulnDistributionIntro, reducedIntro, highDescription, medDescription, lowDescription, noneDescription, noVulnDescription, infoDescription, degreeIntro, riskIntro, footer", "safe"),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
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
}