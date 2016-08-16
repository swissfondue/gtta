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
     * @var string footer.
     */
    public $footer;

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
            array("footer, highDescription, medDescription, lowDescription, noneDescription, noVulnDescription, infoDescription, localizedItems", "safe"),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "type" => Yii::t("app", "Type"),
			"name" => Yii::t("app", "Name"),
			"footer" => Yii::t("app", "Footer"),
            "highDescription" => Yii::t("app", "High Risk Description"),
            "medDescription" => Yii::t("app", "Med Risk Description"),
            "lowDescription" => Yii::t("app", "Low Risk Description"),
            "noneDescription" => Yii::t("app", "No Test Done Description"),
            "noVulnDescription" => Yii::t("app", "No Vulnerability Description"),
            "infoDescription" => Yii::t("app", "Info Description"),
		);
	}
}