<?php

/**
 * This is the model class for check solution edit form.
 */
class CheckSolutionEditForm extends LocalizedFormModel
{
	/**
     * @var string solution.
     */
    public $solution;

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
			array( 'solution, sortOrder', 'required' ),
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
			'solution'  => Yii::t('app', 'Solution'),
            'sortOrder' => Yii::t('app', 'Sort Order'),
		);
	}
}