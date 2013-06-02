<?php

/**
 * This is the model class for table "gt_dependency_processors".
 *
 * The followings are the available columns in table 'gt_dependency_processors':
 * @property integer $id
 * @property string $name
 */
class GtDependencyProcessor extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtDependencyProcessor the static model class
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
		return 'gt_dependency_processors';
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
}