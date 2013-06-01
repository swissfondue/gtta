<?php

/**
 * This is the model class for table "project_gt_check_inputs".
 *
 * The followings are the available columns in table 'project_gt_check_inputs':
 * @property integer $project_id
 * @property integer $gt_check_id
 * @property integer $check_input_id
 * @property string $value
 * @property string $file
 */
class ProjectGtCheckInput extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtCheckInput the static model class
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
		return 'project_gt_check_inputs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('project_id, check_input_id, gt_check_id', 'required'),
            array('project_id, check_input_id, gt_check_id', 'numerical', 'integerOnly' => true),
            array('file', 'length', 'max' => 1000),
            array('value', 'safe'),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'input' => array(self::BELONGS_TO, 'CheckInput', 'check_input_id'),
            'check' => array(self::BELONGS_TO, 'GtCheck', 'gt_check_id'),
		);
	}
}