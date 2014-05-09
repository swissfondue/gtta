<?php

/**
 * This is the model class for table "project_gt_check_solutions".
 *
 * The followings are the available columns in table 'project_gt_check_solutions':
 * @property integer $project_id
 * @property integer $gt_check_id
 * @property integer $check_solution_id
 */
class ProjectGtCheckSolution extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtCheckSolution the static model class
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
		return 'project_gt_check_solutions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('project_id, check_solution_id, gt_check_id', 'required'),
            array('project_id, check_solution_id, gt_check_id', 'numerical', 'integerOnly' => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'solution' => array(self::BELONGS_TO, 'CheckSolution', 'check_solution_id'),
            'check' => array(self::BELONGS_TO, 'GtCheck', 'gt_check_id'),
		);
	}
}
