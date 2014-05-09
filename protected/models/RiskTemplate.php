<?php

/**
 * This is the model class for table "risk_templates".
 *
 * The followings are the available columns in table 'risk_templates':
 * @property integer $id
 * @property string $name
 */
class RiskTemplate extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RiskTemplate the static model class
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
		return 'risk_templates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name', 'required' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'       => array( self::HAS_MANY, 'RiskTemplateL10n', 'risk_template_id' ),
            'categories' => array( self::HAS_MANY, 'RiskCategory',     'risk_template_id' ),
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
