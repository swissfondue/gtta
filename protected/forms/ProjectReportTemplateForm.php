<?php

/**
 * This is the model class for project report template form.
 */
class ProjectReportTemplateForm extends FormModel {
    /**
     * @var integer template id.
     */
    public $reportTemplateId;

    /**
     * @var bool custom report
     */
    public $customReport;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["reportTemplateId", "required"],
            ["reportTemplateId", "checkTemplate"],
            ["customReport", "boolean"],
		];
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return [
			"reportTemplateId" => Yii::t("app", "Template"),
			"customReport" => Yii::t("app", "Custom Report"),
		];
	}

    /**
     * Check template
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkTemplate($attribute, $params) {
        $template = ReportTemplate::model()->findByPk($this->reportTemplateId);

        if (!$template) {
            $this->addError("reportTemplateId", Yii::t("app", "Template not found."));
            return false;
        }

        return true;
    }
}