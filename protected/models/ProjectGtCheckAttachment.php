<?php

/**
 * This is the model class for table "project_gt_check_attachments".
 *
 * The followings are the available columns in table 'project_gt_check_attachments':
 * @property integer $project_id
 * @property integer $gt_check_id
 * @property string $name
 * @property string $type
 * @property string $path
 * @property integer $size
 */
class ProjectGtCheckAttachment extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtCheckAttachment the static model class
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
		return 'project_gt_check_attachments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('project_id, gt_check_id, name, type, path, size', 'required'),
            array('project_id, gt_check_id, size', 'numerical', 'integerOnly' => true),
            array('name, type, path', 'length', 'max' => 1000),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'project' => array( self::BELONGS_TO, 'Project', 'project_id' ),
            'check' => array( self::BELONGS_TO, 'GtCheck', 'gt_check_id' ),
		);
	}
}
