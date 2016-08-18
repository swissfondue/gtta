<?php

/**
 * This is the model class for project comparison form.
 */
class ProjectComparisonForm extends CFormModel
{
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
     * @var integer second project id.
     */
    public $projectId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["fontSize, fontFamily, pageMargin, cellPadding, projectId", "required"],
            ["fontSize", "numerical", "integerOnly" => true, "min" => Yii::app()->params["reports"]["minFontSize"], "max" => Yii::app()->params["reports"]["maxFontSize"]],
            ["cellPadding", "numerical", "min" => Yii::app()->params["reports"]["minCellPadding"], "max" => Yii::app()->params["reports"]["maxCellPadding"]],
            ["pageMargin", "numerical", "min" => Yii::app()->params["reports"]["minPageMargin"], "max" => Yii::app()->params["reports"]["maxPageMargin"]],
            ["fontFamily", "in", "range" => Yii::app()->params["reports"]["fonts"]],
            ["projectId", "checkProject"],
		];
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return [
            'fontSize' => Yii::t('app', 'Font Size'),
            'fontFamily' => Yii::t('app', 'Font Family'),
            'pageMargin' => Yii::t('app', 'Page Margin'),
            'cellPadding' => Yii::t('app', 'Cell Padding'),
        ];
    }

    /**
     * Check project
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkProject($attribute, $params) {
        /** @var Project $p */
        $p = Project::model()->findByPk($this->projectId);

        if (!$p || !$p->checkPermission()) {
            $this->addError("projectId", Yii::t("app", "Project not found."));
            return false;
        }

        return true;
    }
}