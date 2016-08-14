<?php

/**
 * This is the model class for table "report_templates_l10n".
 *
 * The followings are the available columns in table 'report_templates_l10n':
 * @property integer $report_template_id
 * @property integer $language_id
 * @property string $name
 * @property string $high_description
 * @property string $low_description
 * @property string $med_description
 * @property string $footer
 * @property string $none_description
 * @property string $no_vuln_description
 * @property string $info_description
 */
class ReportTemplateL10n extends ActiveRecord {
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
		return "report_templates_l10n";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["report_template_id, language_id", "required"],
            ["name", "length", "max" => 1000],
            ["report_template_id, language_id", "numerical", "integerOnly" => true],
            ["footer, high_description, med_description, low_description, info_description, none_description, no_vuln_description", "safe"],
		];
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return [
            "template" => [self::BELONGS_TO, "ReportTemplate", "report_template_id"],
            "language" => [self::BELONGS_TO, "Language", "language_id"],
		];
	}
}
