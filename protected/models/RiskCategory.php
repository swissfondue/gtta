<?php

/**
 * This is the model class for table "risk_categories".
 *
 * The followings are the available columns in table 'risk_categories':
 * @property integer $id
 * @property string $name
 * @property integer $risk_category_id
 */
class RiskCategory extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RiskCategory the static model class
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
		return 'risk_categories';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name, risk_template_id', 'required' ),
            array( 'risk_template_id', 'numerical', 'integerOnly' => true ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'     => array( self::HAS_MANY,   'RiskCategoryL10n',  'risk_category_id' ),
            'checks'   => array( self::HAS_MANY,   'RiskCategoryCheck', 'risk_category_id' ),
            'template' => array( self::BELONGS_TO, 'RiskTemplate',      'risk_template_id' ),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->name != NULL ? $this->l10n[0]->name : $this->name;

        return $this->name;
    }
}
