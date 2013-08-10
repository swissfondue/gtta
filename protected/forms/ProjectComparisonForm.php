<?php

/**
 * This is the model class for project comparison form.
 */
class ProjectComparisonForm extends CFormModel
{
    /**
     * @var string font size.
     */
    public $fontSize;

    /**
     * @var string font family.
     */
    public $fontFamily;

    /**
     * @var float page margin.
     */
    public $pageMargin;

    /**
     * @var float cell padding.
     */
    public $cellPadding;

    /**
     * @var integer client id.
     */
    public $clientId;

    /**
     * @var integer first project id.
     */
    public $projectId1;

    /**
     * @var integer second project id.
     */
    public $projectId2;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'fontSize, fontFamily, pageMargin, cellPadding', 'required' ),
            array( 'fontSize', 'numerical', 'integerOnly' => true, 'min' => Yii::app()->params['reports']['minFontSize'], 'max' => Yii::app()->params['reports']['maxFontSize'] ),
            array( 'cellPadding', 'numerical', 'min' => Yii::app()->params['reports']['minCellPadding'], 'max' => Yii::app()->params['reports']['maxCellPadding'] ),
            array( 'pageMargin', 'numerical', 'min' => Yii::app()->params['reports']['minPageMargin'], 'max' => Yii::app()->params['reports']['maxPageMargin'] ),
            array( 'fontFamily', 'in', 'range' => Yii::app()->params['reports']['fonts'] ),
            array( 'clientId, projectId1, projectId2', 'safe' ),
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'fontSize'    => Yii::t('app', 'Font Size'),
            'fontFamily'  => Yii::t('app', 'Font Family'),
            'pageMargin'  => Yii::t('app', 'Page Margin'),
            'cellPadding' => Yii::t('app', 'Cell Padding'),
        );
    }
}