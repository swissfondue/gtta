<?php

/**
 * This is the model class for table "gt_categories".
 *
 * The followings are the available columns in table 'gt_categories':
 * @property integer $id
 * @property string $name
 */
class GtCategory extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtCategory the static model class
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
		return 'gt_categories';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('name', 'required'),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n' => array(self::HAS_MANY, 'GtCategoryL10n', 'gt_category_id'),
            'types' => array(self::HAS_MANY, 'GtType', 'gt_category_id'),
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
