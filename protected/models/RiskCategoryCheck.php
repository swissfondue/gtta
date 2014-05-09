<?php

/**
 * This is the model class for table "risk_category_checks".
 *
 * The followings are the available columns in table 'risk_category_checks':
 * @property integer $risk_category_id
 * @property integer $check_id
 * @property integer $damage
 * @property integer $likelihood
 */
class RiskCategoryCheck extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RiskCategoryCheck the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'risk_category_checks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'risk_category_id, check_id', 'required' ),
            array( 'risk_category_id, check_id, damage, likelihood', 'numerical', 'integerOnly' => true ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'category' => array( self::BELONGS_TO, 'RiskCategory', 'risk_category_id' ),
            'check'    => array( self::BELONGS_TO, 'Check',        'check_id' ),
		);
	}
}
