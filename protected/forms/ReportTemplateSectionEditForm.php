<?php

/**
 * This is the model class for report template section edit form.
 */
class ReportTemplateSectionEditForm extends FormModel {
    /**
     * Scenarios
     */
    const SCENARIO_SECTION = "section";

    /**
     * @var int id.
     */
    public $id;

	/**
     * @var string title.
     */
    public $title;

    /**
     * @var string content.
     */
    public $content;

    /**
     * @var int type
     */
    public $type;

    /**
     * @var array order
     */
    public $order;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
			["title, type", "required", "on" => self::SCENARIO_SECTION],
            ["order", "required"],
            ["title", "length", "max" => 1000],
            ["id, type", "numerical", "integerOnly" => true],
            ["type", "in", "range" => ReportSection::getValidTypes()],
            ["content", "safe"],
            ["order", "checkOrder"],
		];
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return [
			"title" => Yii::t("app", "Title"),
			"type" => Yii::t("app", "Type"),
            "content" => Yii::t("app", "Content"),
		];
	}

    /**
     * Check order
     * @param $attribute
     * @param $params
     * @return boolean
     */
    public function checkOrder($attribute, $params) {
        $this->order = @json_decode($this->order, true);

        if (!$this->order) {
            $this->order = [];
        }

        return true;
    }
}