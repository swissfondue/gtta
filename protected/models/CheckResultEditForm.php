<?php

/**
 * This is the model class for check result edit form.
 */
class CheckResultEditForm extends LocalizedFormModel
{
    /**
     * @var string title.
     */
    public $title;

	/**
     * @var string result.
     */
    public $result;

    /**
     * @var integer sort order.
     */
    public $sortOrder;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'title, result, sortOrder', 'required' ),
            array( 'title', 'length', 'max' => 1000 ),
            array( 'sortOrder', 'numerical', 'integerOnly' => true, 'min' => 0 ),
            array( 'localizedItems', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'title'     => Yii::t('app', 'Title'),
			'result'    => Yii::t('app', 'Result'),
            'sortOrder' => Yii::t('app', 'Sort Order'),
		);
	}
}