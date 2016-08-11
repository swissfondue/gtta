<?php

/**
 * This is the model class for table "report_template_sections".
 *
 * The followings are the available columns in table "report_template_sections":
 * @property integer $id
 * @property string $report_template_id
 * @property integer $type
 * @property string $title
 * @property string $content
 * @property integer $order
 *
 * @property ReportTemplate $template
 */
class ReportTemplateSection extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ReportTemplateSection the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "report_template_sections";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["title, report_template_id, title, type", "required"],
            ["title", "length", "max" => 1000],
            ["report_template_id, order", "numerical", "integerOnly" => true],
            ["type", "in", "range" => ReportSection::getValidTypes()],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            "template" => [self::BELONGS_TO, "ReportTemplate", "report_template_id"],
        ];
    }
}
