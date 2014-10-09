<?php

/**
 * This is the model class for project report form.
 */
class ProjectReportForm extends CFormModel {
    const INFO_LOCATION_TARGET = "target";
    const INFO_LOCATION_TABLE = "table";
    const INFO_LOCATION_APPENDIX = "appendix";

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
     * @var integer client id.
     */
    public $clientId;

    /**
     * @var integer project id.
     */
    public $projectId;

    /**
     * @var array target ids.
     */
    public $targetIds;

    /**
     * @var integer template id.
     */
    public $templateId;

    /**
     * @var integer risk template id.
     */
    public $riskTemplateId;

    /**
     * @var array options (title page, reduced vuln list, risk matrix, degree)
     */
    public $options;

    /**
     * @var type of report file (rtf | zip)
     */
    public $fileType;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("fontSize, fontFamily, pageMargin, cellPadding, fileType", "required"),
            array("fontSize", "numerical", "integerOnly" => true, "min" => Yii::app()->params["reports"]["minFontSize"], "max" => Yii::app()->params["reports"]["maxFontSize"]),
            array("cellPadding", "numerical", "min" => Yii::app()->params["reports"]["minCellPadding"], "max" => Yii::app()->params["reports"]["maxCellPadding"]),
            array("pageMargin", "numerical", "min" => Yii::app()->params["reports"]["minPageMargin"], "max" => Yii::app()->params["reports"]["maxPageMargin"]),
            array("fontFamily", "in", "range" => Yii::app()->params["reports"]["fonts"]),
            array("infoChecksLocation", "in", "range" => array(self::INFO_LOCATION_TARGET, self::INFO_LOCATION_TABLE, self::INFO_LOCATION_APPENDIX)),
            array("clientId, projectId, targetIds, options, templateId", "safe"),
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            "fontSize" => Yii::t("app", "Font Size"),
            "fontFamily" => Yii::t("app", "Font Family"),
            "pageMargin" => Yii::t("app", "Page Margin"),
            "cellPadding" => Yii::t("app", "Cell Padding"),
            "clientId" => Yii::t("app", "Client"),
			"projectId" => Yii::t("app", "Project"),
			"targetIds" => Yii::t("app", "Targets"),
		);
	}
}