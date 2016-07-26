<?php

/**
 * This is the model class for table "report_templates".
 *
 * The followings are the available columns in table 'report_templates':
 * @property integer $id
 * @property string $name
 * @property string $header_image_path
 * @property string $header_image_type
 * @property integer $type
 * @property string $file_path
 */
class ReportTemplate extends ActiveRecord {
    /**
     * Constants
     */
    const TYPE_RTF = 0;
    const TYPE_DOCX = 1;

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportTemplate the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "report_templates";
	}

    /**
     * Get valid types
     * @return array
     */
    public static function getValidTypeNames() {
        return array(
            self::TYPE_RTF => Yii::t("app", "RTF"),
            self::TYPE_DOCX => Yii::t("app", "DOCX"),
        );
    }

    /**
     * Get valid types
     * @return array
     */
    public static function getValidTypes() {
        return array(
            self::TYPE_RTF,
            self::TYPE_DOCX
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name, type", "required"),
            array("name, header_image_path, header_image_type, file_path", "length", "max" => 1000),
            array("type", "in", "range" => self::getValidTypes()),
            array("sections", "checkSections")
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "l10n" => array(self::HAS_MANY, "ReportTemplateL10n", "report_template_id"),
            "summary" => array(self::HAS_MANY, "ReportTemplateSummary", "report_template_id"),
            "vulnSections" => array(self::HAS_MANY, "ReportTemplateVulnSection", "report_template_id"),
            "ratingImages" => array(self::HAS_MANY, "ReportTemplateVulnSection", "report_template_id"),
            "sections" => array(self::HAS_MANY, "ReportTemplateSection", "report_template_id", "order" => "sections.order ASC"),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->name != NULL ? $this->l10n[0]->name : $this->name;
        }

        return $this->name;
    }

    /**
     * Returns high rating image if current template
     * @return CActiveRecord
     */
    public function getRatingImage($id) {
        $id = (int) $id;

        $criteria = new CDbCriteria();
        $criteria->addCondition('report_template_id =:report_template_id');
        $criteria->addCondition('rating_id =:rating_id');
        $criteria->params = array(
            'report_template_id' => $this->id,
            'rating_id' => $id
        );

        return ReportTemplateRatingImage::model()->find($criteria);
    }

    /**
     * Check report section
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkSections($attribute, $params) {
        return false;
    }

    public function jsonSections($languageId = null) {
        $result = [];

        if ($languageId) {

        } else {
            foreach ($this->sections as $section) {
                $result[] = [
                    "title" => $section->title,
                    "content" => $section->content
                ];
            }
        }
    }
}
