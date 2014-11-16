<?php

/**
 * This is the model class for target edit form.
 */
class TargetEditForm extends CFormModel
{
	/**
     * @var string host.
     */
    public $host;

    /**
     * @var integer port
     */
    public $port;

    /**
     * @var string description.
     */
    public $description;

    /**
     * @var array category ids.
     */
    public $categoryIds;

    /**
     * @var array reference ids.
     */
    public $referenceIds;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'host', 'required' ),
            array( 'host, description', 'length', 'max' => 1000 ),
            array("port", "numerical", "integerOnly" => true, "min" => 1, "max" => 65535),
            array( 'categoryIds, referenceIds', 'safe' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'host'        => Yii::t('app', 'Host'),
            'description' => Yii::t('app', 'Description'),
            "port" => Yii::t("app", "Port"),
		);
	}
}