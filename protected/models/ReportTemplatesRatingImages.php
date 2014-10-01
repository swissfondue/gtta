<?php

/**
 * This is the model class for table "report_templates_rating_images".
 *
 * The followings are the available columns in table 'report_templates_rating_images':
 * @property integer $template_id
 * @property integer $rating_id
 * @property string $image_type
 * @property string $image_path
 */
class ReportTemplatesRatingImages extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Check the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "report_templates_rating_images";
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "reportTemplate" => array(self::BELONGS_TO, "ReportTemplate", "report_template_id"),
        );
    }
}

