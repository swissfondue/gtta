<?php

/**
 * This is the model class for project vulnerabilities export form.
 */
class VulnExportReportForm extends FormModel {
    /**
     * @var array target ids.
     */
    public $targetIds;

    /**
     * @var array ratings.
     */
    public $ratings;

    /**
     * @var array columns.
     */
    public $columns;

    /**
     * @var boolean include header.
     */
    public $header;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["ratings, columns", "required"],
            ["header", "boolean"],
            ["targetIds, ratings, columns", "safe"],
		];
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return [
			"targetIds" => Yii::t("app", "Targets"),
            "ratings" => Yii::t("app", "Ratings"),
			"columns" => Yii::t("app", "Columns"),
		];
	}
}