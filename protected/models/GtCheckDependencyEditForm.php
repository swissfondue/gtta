<?php

/**
 * This is the model class for GT dependency edit form.
 */
class GtCheckDependencyEditForm extends CFormModel
{
	/**
     * @var integer module id.
     */
    public $moduleId;

    /**
     * @var string condition.
     */
    public $condition;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('moduleId, condition', 'required'),
            array('moduleId', 'numerical', 'integerOnly' => true, 'min' => 0),
            array('condition', 'length', 'max' => 1000),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'moduleId' => Yii::t('app', 'Module'),
            'condition' => Yii::t('app', 'Condition'),
		);
	}
}