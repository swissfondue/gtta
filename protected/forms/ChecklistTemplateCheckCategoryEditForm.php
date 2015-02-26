<?php

/**
 * Class ChecklistTemplateCheckCategoryEditForm
 */
class ChecklistTemplateCheckCategoryEditForm extends CFormModel {
    /**
     * @var integer categoryId
     */
    public $categoryId;
    /**
     * @var array check
     */
    public $checkIds;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array( 'categoryId', 'required' ),
            array( 'categoryId', 'numerical', 'integerOnly' => true ),
            array( 'categoryId', 'checkCategory' ),
            array( 'checkIds', 'safe' ),
        );
    }

    /**
     * Check if check category exists
     * @param $attributes
     * @param $params
     * @return bool
     */
    public function checkCategory($attributes, $params) {
        $category = CheckCategory::model()->findByPk($this->categoryId);

        if (!$category) {
            $this->addError("categoryId", "Category not found.");
            return false;
        }

        return true;
    }
}