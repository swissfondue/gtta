<?php

/**
 * This is the model class for table "gt_check_dependencies".
 *
 * The followings are the available columns in table 'gt_check_dependencies':
 * @property integer $id
 * @property integer $gt_check_id
 * @property integer $gt_module_id
 * @property string $condition
 */
class GtCheckDependency extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtCheckDependency the static model class
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
		return 'gt_check_dependencies';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('gt_check_id, gt_module_id, condition', 'required'),
            array('gt_check_id, gt_module_id', 'numerical', 'integerOnly' => true),
            array('condition', 'length', 'max' => 1000),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'check' => array(self::BELONGS_TO, 'GtCheck', 'gt_check_id'),
            'module' => array(self::BELONGS_TO, 'GtModule', 'gt_module_id'),
		);
	}
}
