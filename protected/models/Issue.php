<?php

/**
 * This is the model class for table "issues".
 *
 * The followings are the available columns in table 'checks':
 * @property integer $id
 * @property integer $project_id
 * @property integer $check_id
 * @property string name
 */
class Issue extends ActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Check the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return "issues";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ["project_id, check_id, name", "required"],
            ["project_id, check_id", "numerical", "integerOnly" => true],
            ["name", "length", "max" => 1000],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            "project" => [self::BELONGS_TO, "Project", "project_id"],
            "check" => [self::BELONGS_TO, "Check", "check_id"],
        ];
    }
}