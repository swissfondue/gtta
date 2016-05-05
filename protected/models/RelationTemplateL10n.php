<?php

/**
 * This is the model class for table "relation_templates_l10n".
 *
 * The followings are the available columns in table 'relation_templates_l10n':
 * @property integer $relation_template_id
 * @property integer $language_id
 * @property string $name
 */
class RelationTemplateL10n extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ReportTemplateL10n the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "relation_templates_l10n";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("relation_template_id, language_id", "required"),
            array("name", "length", "max" => 1000),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "template" => array(self::BELONGS_TO, "RelationTemplate", "relation_template_id"),
            "language" => array(self::BELONGS_TO, "Language", "language_id"),
        );
    }
}