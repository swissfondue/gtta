<?php

/**
 * This is the model class for table "check_categories".
 *
 * The followings are the available columns in table 'check_categories':
 * @property integer $id
 * @property string $name
 */
class CheckCategory extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckCategory the static model class
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
		return 'check_categories';
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
            'l10n'   => array( self::HAS_MANY, 'CheckCategoryL10n', 'check_category_id' ),
            'checks' => array( self::HAS_MANY, 'Check',             'check_category_id' ),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->name == NULL ? $this->l10n[0]->name : $this->name;

        return $this->name;
    }
}