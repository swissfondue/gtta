<?php

/**
 * This is the model class for check input edit form.
 */
class CheckInputEditForm extends LocalizedFormModel
{
	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string type.
     */
    public $type;

    /**
     * @var string description.
     */
    public $description;

    /**
     * @var string value.
     */
    public $value;

    /**
     * @var integer sort order.
     */
    public $sortOrder;

    /**
     * @var boolean visible.
     */
    public $visible;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("name, sortOrder", "required"),
            array("name", "length", "max" => 1000),
            array("sortOrder", "numerical", "integerOnly" => true, "min" => 0),
            array("visible", "boolean"),
            array("type", "in", "range" => array(
                CheckInput::TYPE_TEXT,
                CheckInput::TYPE_TEXTAREA,
                CheckInput::TYPE_CHECKBOX,
                CheckInput::TYPE_RADIO,
                CheckInput::TYPE_FILE
            )),
            array("localizedItems, description, value", "safe"),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			"name" => Yii::t("app", "Name"),
            "type" => Yii::t("app", "Type"),
            "description" => Yii::t("app", "Description"),
            "value" => Yii::t("app", "Value"),
            "sortOrder" => Yii::t("app", "Sort Order"),
            "visible" => Yii::t("app", "Visible"),
		);
	}
}