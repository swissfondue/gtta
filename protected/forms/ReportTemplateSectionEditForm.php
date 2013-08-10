<?php

/**
 * This is the model class for report template section edit form.
 */
class ReportTemplateSectionEditForm extends LocalizedFormModel
{
    /**
     * @var string intro.
     */
    public $intro;

    /**
     * @var string title.
     */
    public $title;

    /**
     * @var integer sort order.
     */
    public $sortOrder;

    /**
     * @var integer category id.
     */
    public $categoryId;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'title, categoryId, sortOrder', 'required' ),
            array( 'title', 'length', 'max' => 1000 ),
            array( 'sortOrder', 'numerical', 'integerOnly' => true, 'min' => 0 ),
            array( 'categoryId', 'numerical', 'integerOnly' => true ),
            array( 'categoryId', 'checkCategory' ),
            array( 'localizedItems, intro', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'title'      => Yii::t('app', 'Title'),
			'categoryId' => Yii::t('app', 'Category'),
            'sortOrder'  => Yii::t('app', 'Sort Order'),
            'intro'      => Yii::t('app', 'Introduction'),
		);
	}

    /**
	 * Checks if check category exists.
	 */
	public function checkCategory($attribute, $params)
	{
		$category = CheckCategory::model()->findByPk($this->categoryId);

        if (!$category)
        {
            $this->addError('categoryId', Yii::t('app', 'Category not found.'));
            return false;
        }

        return true;
	}
}