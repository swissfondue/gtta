<?php

/**
 * This is the model class for risk template edit form.
 */
class RiskTemplateEditForm extends LocalizedFormModel
{
	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string intro.
     */
    public $intro;

    /**
     * @var string appendix.
     */
    public $appendix;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'localizedItems, intro, appendix', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('app', 'Name'),
		);
	}
}