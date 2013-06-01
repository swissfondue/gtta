<?php

/**
 * This is the model class for table "gt_modules".
 *
 * The followings are the available columns in table 'gt_modules':
 * @property integer $id
 * @property integer $gt_type_id
 * @property string $name
 */
class GtModule extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtModule the static model class
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
		return 'gt_modules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('name, gt_type_id', 'required'),
            array('gt_type_id', 'numerical', 'integerOnly' => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n' => array(self::HAS_MANY, 'GtModuleL10n', 'gt_module_id'),
            'checks' => array(self::HAS_MANY, 'GtCheck', 'gt_module_id'),
            'type' => array(self::BELONGS_TO, 'GtType', 'gt_type_id'),
            'checkCount' => array(self::STAT, 'GtCheck', 'gt_module_id'),
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