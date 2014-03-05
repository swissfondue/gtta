<?php

/**
 * This is the model class for project planner edit form.
 */
class ProjectPlannerEditForm extends CFormModel {
	/**
     * @var int user id
     */
    public $userId;

    /**
     * @var int project id
     */
    public $projectId;

    /**
     * @var int module id
     */
    public $moduleId;

    /**
     * @var int target id
     */
    public $targetId;

    /**
     * @var int category id
     */
    public $categoryId;

    /**
     * @var string start date.
     */
    public $startDate;

    /**
     * @var string end date.
     */
    public $endDate;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("userId, projectId", "required"),
            array("startDate, endDate", "date", "allowEmpty" => false, "format" => "yyyy-MM-dd"),
            array("userId, projectId, moduleId, targetId, categoryId", "numerical", "integerOnly" => true),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			"userId" => Yii::t("app", "User"),
            "projectId" => Yii::t("app", "Project"),
            "moduleId" => Yii::t("app", "Module"),
            "targetId" => Yii::t("app", "Target"),
            "categoryId" => Yii::t("app", "Category"),
            "startDate" => Yii::t("app", "Start Date"),
            "endDate" => Yii::t("app", "End Date"),
		);
	}
}