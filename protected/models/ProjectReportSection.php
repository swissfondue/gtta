<?php

/**
 * This is the model class for table "project_report_sections".
 *
 * The followings are the available columns in table "project_report_sections":
 * @property integer $id
 * @property string $project_id
 * @property integer $type
 * @property string $title
 * @property integer $content
 * @property integer $sort_order
 *
 * @property Project $project
 */
class ProjectReportSection extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ProjectReportSection the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "project_report_sections";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["title, project_id, title, type", "required"],
            ["title", "length", "max" => 1000],
            ["project_id, sort_order", "numerical", "integerOnly" => true],
            ["type", "in", "range" => ReportSection::getValidTypes()],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "project" => [self::BELONGS_TO, "Project", "project_id"],
        ];
    }
}
