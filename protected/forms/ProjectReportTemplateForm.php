<?php

/**
 * This is the model class for project report template form.
 */
class ProjectReportTemplateForm extends CFormModel {
    /**
     * @var integer template id.
     */
    public $templateId;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["templateId", "required"],
            ["templateId", "checkTemplate"],
		];
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return [
			"templateId" => Yii::t("app", "Template"),
		];
	}

    /**
     * Check template
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkTemplate($attribute, $params) {
        $template = ReportTemplate::model()->findByPk($this->templateId);

        if (!$template) {
            $this->addError("templateId", Yii::t("app", "Template not found."));
            return false;
        }

        return true;
    }
}