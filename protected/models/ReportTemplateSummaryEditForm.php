<?php

/**
 * This is the model class for report template summary edit form.
 */
class ReportTemplateSummaryEditForm extends LocalizedFormModel
{
	/**
     * @var string title.
     */
    public $title;

    /**
     * @var string summary.
     */
    public $summary;

    /**
     * @var float rating from.
     */
    public $ratingFrom;

    /**
     * @var float rating to.
     */
    public $ratingTo;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'title, ratingFrom, ratingTo', 'required' ),
            array( 'title', 'length', 'max' => 1000 ),
            array( 'ratingFrom, ratingTo', 'numerical', 'min' => 0.0, 'max' => 5.0 ),
            array( 'localizedItems, summary', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'title'      => Yii::t('app', 'Title'),
            'summary'    => Yii::t('app', 'Summary'),
            'ratingFrom' => Yii::t('app', 'Rating From'),
            'ratingTo'   => Yii::t('app', 'Rating To'),
		);
	}
}