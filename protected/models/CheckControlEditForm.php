<?php

/**
 * This is the model class for check control edit form.
 */
class CheckControlEditForm extends LocalizedFormModel
{
	/**
     * @var string name.
     */
    public $name;

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
			array( 'name, categoryId', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'categoryId', 'numerical', 'integerOnly' => true ),
            array( 'categoryId', 'checkCategory' ),
            array( 'localizedItems', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'       => Yii::t('app', 'Name'),
            'categoryId' => Yii::t('app', 'Category'),
		);
	}

    /**
	 * Checks if category exists.
	 */
	public function checkCategory($attribute, $params)
	{
		$category = CheckCategory::model()->findByPk($this->categoryId);

        if (!$category)
        {
            $this->addError('categoryId', Yii::t('app', 'Category doesn\\\'t exist.'));
            return false;
        }

        return true;
	}
}