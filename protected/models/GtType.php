<?php

/**
 * This is the model class for table "gt_types".
 *
 * The followings are the available columns in table 'gt_types':
 * @property integer $id
 * @property integer $gt_category_id
 * @property string $name
 */
class GtType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtType the static model class
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
		return 'gt_types';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('name, gt_category_id', 'required'),
            array('gt_category_id', 'numerical', 'integerOnly' => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n' => array(self::HAS_MANY, 'GtTypeL10n', 'gt_type_id'),
            'modules' => array(self::HAS_MANY, 'GtModule', 'gt_type_id'),
            'category' => array(self::BELONGS_TO, 'GtCategory', 'gt_category_id'),
            'moduleCount' => array(self::STAT, 'GtModule', 'gt_type_id'),
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
