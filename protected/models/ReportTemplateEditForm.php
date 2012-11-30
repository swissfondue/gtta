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
     * @var string vulns intro.
     */
    public $vulnsIntro;

    /**
     * @var string info checks intro.
     */
    public $infoChecksIntro;

    /**
     * @var string security level intro.
     */
    public $securityLevelIntro;

    /**
     * @var string vuln distribution intro.
     */
    public $vulnDistributionIntro;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'intro, appendix, localizedItems, vulnsIntro, infoChecksIntro, securityLevelIntro, vulnDistributionIntro', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'                  => Yii::t('app', 'Name'),
            'intro'                 => Yii::t('app', 'Introduction'),
            'appendix'              => Yii::t('app', 'Appendix'),
            'vulnsIntro'            => Yii::t('app', 'Vulns Introduction'),
            'infoChecksIntro'       => Yii::t('app', 'Info Checks Introduction'),
            'securityLevelIntro'    => Yii::t('app', 'Security Level Introduction'),
            'vulnDistributionIntro' => Yii::t('app', 'Vuln Distribution Introduction'),
		);
	}
}