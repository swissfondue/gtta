<?php

/**
 * This is the model class for table "gt_modules_l10n".
 *
 * The followings are the available columns in table 'gt_modules_l10n':
 * @property integer $gt_module_id
 * @property integer $language_id
 * @property string $name
 */
class GtModuleL10n extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtModuleL10n the static model class
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
		return 'gt_modules_l10n';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('gt_module_id, language_id', 'required'),
            array('name', 'length', 'max' => 1000),
            array('gt_module_id, language_id', 'numerical', 'integerOnly' => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'module' => array(self::BELONGS_TO, 'GtModule', 'gt_module_id'),
            'language' => array(self::BELONGS_TO, 'Language', 'language_id'),
		);
	}
}
