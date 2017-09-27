<?php

/**
 * This is the model class for table "checks".
 *
 * The followings are the available columns in table 'checks':
 * @property integer $id
 * @property integer $check_control_id
 * @property string $name
 * @property boolean $automated
 * @property boolean $multiple_solutions
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
 * @property CheckField[] $fields
 */
class Check extends ActiveRecord {
    const STATUS_INSTALLED = 1;
    const STATUS_SHARE = 2;

    // nearest sort order
    public $nearest_sort_order;

    /**
     * check edit view constants
     */
    const VIEW_TABBED = 0;
    const VIEW_SHARED = 1;

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
                "check_control_id, reference_id, effort, sort_order, external_id, status",
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
     * Get localized name
     * @return mixed|null|string
     */
    public function getLocalizedName() {
        $language = System::model()->findByPk(1)->language;

        $translate = CheckL10n::model()->findByAttributes([
            "check_id" => $this->id,
            "language_id" => $language->id
        ]);

        if ($translate && $translate->name) {
            return $translate->name;
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
     * @return mixed|null
     * @throws Exception
     */
    private function _getFieldValue($field) {
        $language = System::model()->findByPk(1)->language;

        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN check_fields cf ON cf.id = t.check_field_id";
        $criteria->join .= " LEFT JOIN global_check_fields gcf ON gcf.id = cf.global_check_field_id";
        $criteria->addColumnCondition([
            "cf.check_id" => $this->id,
            "gcf.name" => $field,
            "t.language_id" => $language->id
        ]);

        $field = CheckFieldL10n::model()->find($criteria);

        if (!$field) {
            return null;
        }

        return $field->value;
    }

    /**
     * Return `background_info` field value
     * @return mixed|null
     */
    public function getBackgroundInfo() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_BACKGROUND_INFO);
    }

    /**
     * Return `question` field value
     * @return mixed|null
     */
    public function getQuestion() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_QUESTION);
    }

    /**
     * Return `hints` field value
     * @return mixed|null
     */
    public function getHints() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_HINTS);
    }

    /**
     * Return `result` field value
     * @return mixed|null
     */
    public function getResult() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_RESULT);
    }

    /**
     * Return `application protocol` field value
     * @return mixed|null
     * @throws Exception
     */
    public function getApplicationProtocol() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_APPLICATION_PROTOCOL);
    }

    /**
     * Return `transport protocol` field value
     * @return mixed|null
     * @throws Exception
     */
    public function getTransportProtocol() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_TRANSPORT_PROTOCOL);
    }

    /**
     * Return `port` field value
     * @return mixed|null
     * @throws Exception
     */
    public function getPort() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_PORT);
    }

    /**
     * Return `override target` field value
     * @return mixed|null
     * @throws Exception
     */
    public function getOverrideTarget() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_OVERRIDE_TARGET);
    }

    /**
     * Return `solution` field value
     * @return mixed|null
     * @throws Exception
     */
    public function getSolution() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_SOLUTION);
    }

    /**
     * Return `solution title` field value
     * @return mixed|null
     * @throws Exception
     */
    public function getSolutionTitle() {
        return $this->_getFieldValue(GlobalCheckField::FIELD_SOLUTION_TITLE);
    }

    /**
     * Serialize check
     * @param int|null $language
     * @return array
     */
    public function serialize($language = null) {
        $l10n = null;

        if ($language) {
            $l10n = CheckL10n::model()->findByPk([
                "language_id" => $language,
                "check_id" => $this->id
            ]);
        }

        return [
            "id" => $this->id,
            "check_control_id" => $this->check_control_id,
            "name" => $l10n ? $l10n->name : $this->name,
            "automated" => $this->automated,
            "multiple_solutions" => $this->multiple_solutions,
            "protocol" => $this->getApplicationProtocol(),
            "port" => $this->getPort(),
            "reference_id" => $this->reference_id,
            "reference_code" => $this->reference_code,
            "reference_url" => $this->reference_url,
            "effort" => $this->effort,
            "sort_order" => $this->sort_order,
            "status" => $this->status,
            "external_id" => $this->external_id,
            "create_time" => $this->create_time,
            "private" => $this->private,
        ];
    }
}
