<?php

/**
 * This is the model class for project vulnerabilities export form.
 */
class ProjectVulnExportForm extends CFormModel
{
    /**
     * @var array ratings.
     */
    public $ratings;

    /**
     * @var array columns.
     */
    public $columns;

    /**
     * @var boolean include header.
     */
    public $header;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'ratings, columns', 'required' ),
            array( 'header', 'boolean' ),
            array( 'ratings, columns', 'safe' ),
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'ratings' => Yii::t('app', 'Ratings'),
			'columns' => Yii::t('app', 'Columns'),
		);
	}
}