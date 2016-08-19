<?php

/**
 * This is the model class for project report form.
 */
class ProjectReportForm extends CFormModel {
    /**
     * Scenarios
     */
    const SCENARIO_DOCX = "docx";
    const SCENARIO_RTF = "rtf";

    /**
     * Info checks locations
     */
    const INFO_LOCATION_TARGET = "target";
    const INFO_LOCATION_SEPARATE_TABLE = "table";
    const INFO_LOCATION_SEPARATE_SECTION = "section";

    /**
     * Rtf file types
     */
    const FILE_TYPE_RTF = 0;
    const FILE_TYPE_ZIP = 1;

    /**
     * @var string font size.
     */
    public $fontSize;

    /**
     * @var string font family.
     */
    public $fontFamily;

    /**
     * @var float page margin.
     */
    public $pageMargin;

    /**
     * @var float cell padding.
     */
    public $cellPadding;

    /**
     * @var string info checks location
     */
    public $infoChecksLocation;

    /**
     * @var array target ids.
     */
    public $targetIds;

    /**
     * @var integer risk template id.
     */
    public $riskTemplateId;

    /**
     * @var integer risk matrix.
     */
    public $riskMatrix;

    /**
     * @var array title
     */
    public $title;

    /**
     * @var string type of report file (rtf | zip)
     */
    public $fileType;

    /**
     * @var array check fields
     */
    public $fields;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            ["targetIds", "required"],
            ["fontSize, fontFamily, pageMargin, cellPadding, fileType", "required", "on" => self::SCENARIO_RTF],
            ["fontSize", "numerical", "integerOnly" => true, "min" => Yii::app()->params["reports"]["minFontSize"], "max" => Yii::app()->params["reports"]["maxFontSize"]],
            ["cellPadding", "numerical", "min" => Yii::app()->params["reports"]["minCellPadding"], "max" => Yii::app()->params["reports"]["maxCellPadding"]],
            ["pageMargin", "numerical", "min" => Yii::app()->params["reports"]["minPageMargin"], "max" => Yii::app()->params["reports"]["maxPageMargin"]],
            ["fontFamily", "in", "range" => Yii::app()->params["reports"]["fonts"]],
            ["infoChecksLocation", "in", "range" => [self::INFO_LOCATION_TARGET, self::INFO_LOCATION_SEPARATE_TABLE, self::INFO_LOCATION_SEPARATE_SECTION]],
            ["riskMatrix, title", "safe"],
            ["fields", "checkFields"],
            ["riskTemplateId", "checkRiskTemplate"],
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "targetIds" => Yii::t("app", "Targets"),
            "fontSize" => Yii::t("app", "Font Size"),
            "fontFamily" => Yii::t("app", "Font Family"),
            "pageMargin" => Yii::t("app", "Page Margin"),
            "cellPadding" => Yii::t("app", "Cell Padding"),
		);
	}

    /**
     * Check field list
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkFields($attribute, $params) {
        $fields = GlobalCheckField::model()->findAllByAttributes([
            "name" => $this->{$attribute}
        ]);

        if (count($fields) != count($this->{$attribute})) {
            $this->addError("fields", Yii::t("app", "Invalid field list."));

            return false;
        }

        return true;
    }

    /**
     * Check risk template
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkRiskTemplate($attribute, $params) {
        if (!$this->riskTemplateId) {
            return true;
        }

        $template = RiskTemplate::model()->findByPk($this->riskTemplateId);

        if (!$template) {
            $this->addError("riskTemplateId", Yii::t("app", "Risk template not found."));
            return false;
        }

        return true;
    }
}