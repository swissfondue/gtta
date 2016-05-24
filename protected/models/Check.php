<?php

/**
 * This is the model class for table "checks".
 *
 * The followings are the available columns in table 'checks':
 * @property integer $id
 * @property integer $check_control_id
 * @property string $name
 * @property string $background_info
 * @property string $hints
 * @property string $question
 * @property boolean $automated
 * @property boolean $multiple_solutions
 * @property string $protocol
 * @property integer $port
 * @property integer $reference_id
 * @property string $reference_code
 * @property string $reference_url
 * @property integer $effort
 * @property integer $sort_order
 * @property integer $status
 * @property integer $external_id
 * @property string $create_time
 * @property boolean $private
 * @property TargetCheck[] $targetChecks
 * @property CheckL10n[] $l10n
 * @property CheckScript[] $scripts
 */
class Check extends ActiveRecord {
    const STATUS_INSTALLED = 1;
    const STATUS_SHARE = 2;

    // nearest sort order
    public $nearest_sort_order;

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
		return "checks";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name, check_control_id, sort_order, create_time", "required"),
            array("name, protocol, reference_code, reference_url", "length", "max" => 1000),
            array(
                "check_control_id, reference_id, port, effort, sort_order, external_id, status",
                "numerical",
                "integerOnly" => true
            ),
            array("automated, multiple_solutions, private", "boolean"),
            array("status", "in", "range" => array(
                self::STATUS_INSTALLED,
                self::STATUS_SHARE,
            )),
            array("create_time", "safe"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "l10n" => array(self::HAS_MANY, "CheckL10n", "check_id"),
            "control" => array(self::BELONGS_TO, "CheckControl", "check_control_id"),
            "_reference" => array(self::BELONGS_TO, "Reference", "reference_id"),
            "targetChecks" => array(self::HAS_MANY, "TargetCheck", "check_id"),
            "results" => array(self::HAS_MANY, "CheckResult", "check_id"),
            "solutions" => array(self::HAS_MANY, "CheckSolution", "check_id"),
            "scripts" => array(self::HAS_MANY, "CheckScript", "check_id"),
            "riskCategories" => array(self::HAS_MANY, "RiskCategoryCheck", "check_id"),
            "fields" => array(self::HAS_MANY, "CheckField", "check_id"),
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
     * Get status name
     * @return string
     * @throws Exception
     */
    public function getStatusName() {
        $names = array(
            self::STATUS_INSTALLED => Yii::t("app", "Installed"),
            self::STATUS_SHARE => Yii::t("app", "Sharing"),
        );

        if (!isset($names[$this->status])) {
            throw new Exception(Yii::t("app", "Invalid status."));
        }

        return $names[$this->status];
    }

    /**
     * Get field value
     * @param $field
     * @param null $languageId
     * @return mixed|null
     * @throws Exception
     */
    public function getFieldValue($field, $languageId = null) {
        $language = Language::model()->find("\"user_default\" OR \"default\"");

        if ($languageId) {
            $language = Language::model()->findByPk($languageId);

            if (!$language) {
                throw new Exception("Language not exists.");
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition([
            "f.check_id" => $this->id,
            "f.name" => $field,
            "t.language_id" => $language->id
        ]);

        $field = CheckFieldL10n::model()->with([
            "field" => [
                "alias" => "f"
            ]
        ])->find($criteria);

        if (!$field) {
            return null;
        }

        return $field->value;
    }
}
