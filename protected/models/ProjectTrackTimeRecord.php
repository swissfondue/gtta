<?php

/**
 * This is the model class for table "project_track_time_records".
 *
 * The followings are the available columns in table 'target_references':
 * @property integer id (pk)
 * @property integer user_id
 * @property integer project_id
 * @property numeric(11,1) hours
 * @property string description
 * @property timestamp create_time
 */
class ProjectTrackTimeRecord extends ActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TargetReference the static model class
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
        return 'project_track_time_records';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'user_id, project_id, hours', 'required' ),
            array( 'create_time','default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false,'on'=>'insert' )
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user'    => array( self::BELONGS_TO, 'User',    'user_id' ),
            'project' => array( self::BELONGS_TO, 'Project', 'project_id' ),
        );
    }
}
