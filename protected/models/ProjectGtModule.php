<?php

/**
 * This is the model class for table "project_gt_modules".
 *
 * The followings are the available columns in table 'project_gt_modules':
 * @property integer $project_id
 * @property integer $gt_module_id
 * @property integer $sort_order
 * @property integer $max_sort_order
 */
class ProjectGtModule extends ActiveRecord
{
    /**
     * @var integer max sort order.
     */
    public $max_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtModule the static model class
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
		return 'project_gt_modules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('project_id, gt_module_id, sort_order', 'required'),
            array('project_id, gt_module_id, sort_order', 'numerical', 'integerOnly' => true),
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
		);
	}
}
