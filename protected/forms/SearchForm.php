<?php

/**
 * This is the model class for search form.
 */
class SearchForm extends CFormModel
{
	/**
     * @var string query.
     */
    public $query;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'query', 'required' ),
            array( 'query', 'length', 'min' => 3, 'max' => 1000 ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'query' => Yii::t('app', 'Query'),
		);
	}
}