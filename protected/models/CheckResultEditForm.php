<?php

/**
 * This is the model class for check result edit form.
 */
class CheckResultEditForm extends CFormModel
{
	/**
     * @var string result.
     */
    public $result;

    /**
     * @var integer sort order.
     */
    public $sortOrder;

    /**
     * @var array localized items.
     */
    public $localizedItems;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'result, sortOrder', 'required' ),
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
			'result'    => Yii::t('app', 'Result'),
            'sortOrder' => Yii::t('app', 'Sort Order'),
		);
	}
}