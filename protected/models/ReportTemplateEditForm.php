<?php

/**
 * This is the model class for report template edit form.
 */
class ReportTemplateEditForm extends LocalizedFormModel
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
            array( 'intro, appendix, localizedItems', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'     => Yii::t('app', 'Name'),
            'intro'    => Yii::t('app', 'Introduction'),
            'appendix' => Yii::t('app', 'Appendix'),
		);
	}
}