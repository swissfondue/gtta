<?php

/**
 * This is the model class for table "project_gt_suggested_targets".
 *
 * The followings are the available columns in table 'project_gt_suggested_targets':
 * @property integer $id
 * @property integer $project_id
 * @property integer $gt_module_id
 * @property integer $gt_check_id
 * @property string $target
 * @property boolean $approved
 */
class ProjectGtSuggestedTarget extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtSuggestedTarget the static model class
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
		return 'project_gt_suggested_targets';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('project_id, gt_module_id, gt_check_id, target', 'required'),
            array('project_id, gt_module_id, gt_check_id', 'numerical', 'integerOnly' => true),
            array('target', 'length', 'max' => 1000),
            array('approved', 'boolean'),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'module' => array(self::BELONGS_TO, 'GtModule', 'gt_module_id'),
            'check' => array(self::BELONGS_TO, 'GtCheck', 'gt_check_id'),
		);
	}
}