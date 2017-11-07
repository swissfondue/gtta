<?php

/**
 * This is the model class for table "target_custom_checks".
 *
 * The followings are the available columns in table "target_custom_checks":
 * @property integer $id
 * @property integer $target_id
 * @property integer $check_control_id
 * @property integer $user_id
 * @property string $name
 * @property string $background_info
 * @property string $question
 * @property string $result
 * @property string $solution_title
 * @property string $solution
 * @property integer $reference
 * @property integer $rating
 * @property Target $target
 * @property CheckControl $control
 * @property User $user
 * @property TargetCustomCheckAttachment[] $attachments
 * @property integer $vuln_user_id
 * @property date $vuln_deadline
 * @property integer $vuln_status
 * @property timestamp $last_modified
 */
class TargetCustomCheck extends ActiveRecord implements IVariableScopeObject {
    /**
     * Target's check type
     */
    const TYPE = 'custom';

    /**
     * Vuln statuses
     */
    const STATUS_VULN_OPEN = 0;
    const STATUS_VULN_RESOLVED = 100;

    /**
     * Result ratings.
     */
    const RATING_NONE = 0;
    const RATING_NO_VULNERABILITY = 10;
    const RATING_HIDDEN = 20;
    const RATING_INFO = 50;
    const RATING_LOW_RISK = 100;
    const RATING_MED_RISK = 200;
    const RATING_HIGH_RISK = 500;

    /**
     * @var integer max reference.
     */
    public $max_reference;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheck the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "target_custom_checks";
	}

    /**
     * Get valid rating values
     * @return array
     */
    public static function getValidRatings() {
        return array(
            self::RATING_NONE,
            self::RATING_NO_VULNERABILITY,
            self::RATING_HIDDEN,
            self::RATING_INFO,
            self::RATING_LOW_RISK,
            self::RATING_MED_RISK,
            self::RATING_HIGH_RISK,
        );
    }

    /**
     * Get rating names
     * @return array
     */
    public static function getRatingNames() {
        return array(
            self::RATING_NONE => Yii::t("app", "No Test Done"),
            self::RATING_NO_VULNERABILITY => Yii::t("app", "No Vulnerability"),
            self::RATING_HIDDEN => Yii::t("app", "Hidden"),
            self::RATING_INFO =>  Yii::t("app", "Info"),
            self::RATING_LOW_RISK => Yii::t("app", "Low Risk"),
            self::RATING_MED_RISK => Yii::t("app", "Med Risk"),
            self::RATING_HIGH_RISK => Yii::t("app", "High Risk"),
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("target_id, check_control_id, user_id, reference, rating", "required"),
            array("target_id, check_control_id, user_id", "numerical", "integerOnly" => true),
            array("name, solution_title", "length", "max" => 1000),
            array("rating", "in", "range" => self::getValidRatings()),
            array("solution, result, question, background_info", "safe"),
        );
    }

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "target" => array(self::BELONGS_TO, "Target", "target_id"),
            "control" => array(self::BELONGS_TO, "CheckControl", "check_control_id"),
            "user" => array(self::BELONGS_TO, "User", "user_id"),
            "vulnUser" => array(self::BELONGS_TO, "User", "vuln_user_id"),
            "attachments" => array(self::HAS_MANY, "TargetCustomCheckAttachment", "target_custom_check_id"),
		);
	}

    /**
     * @param bool $runValidation
     * @param null $attributes
     * @throws CHttpException
     */
    public function save($runValidation=true, $attributes=null) {
        $dbTargetCheck = TargetCustomCheck::model()->findByPk($this->id);
        $targetCheckIsNew = is_null($dbTargetCheck) || is_null($dbTargetCheck->last_modified);

        if (!$targetCheckIsNew && $this->last_modified < $dbTargetCheck->last_modified) {
            throw new CHttpException(403, Yii::t("app", "Could not modify the target check as there is newer version available. Please reload the page and try again!"));
        }

        $this->last_modified = time();

        parent::save($runValidation, $attributes);
    }

    /**
     * Get variable value
     * @param $name
     * @param VariableScope $scope
     * @return mixed
     * @throws Exception
     */
    public function getVariable($name, VariableScope $scope) {
        $names = $this->getRatingNames();
        $abbreviations = array(
            self::RATING_NONE => "none",
            self::RATING_NO_VULNERABILITY => "no_vuln",
            self::RATING_HIDDEN => "hidden",
            self::RATING_INFO =>  "info",
            self::RATING_LOW_RISK => "low",
            self::RATING_MED_RISK => "med",
            self::RATING_HIGH_RISK => "high",
        );

        $checkData = array(
            "name" => $this->name ? $this->name : "CUSTOM-CHECK-" . $this->reference,
            "background_info" => $this->background_info,
            "hints" => "",
            "question" => $this->question,
            "rating" => $abbreviations[$this->rating],
            "rating_name" => $names[$this->rating],
            "target" => $this->target->host,
            "target_description" => $this->target->description,
            "result" => $this->result,
            "reference" => "CUSTOM-CHECK-" . $this->reference,
            "reference_short" => "CC-" . $this->reference,
            "control" => $control->name,
            "solution" => $this->solution,
        );

        if (!in_array($name, array_keys($checkData))) {
            return "";
        }

        return $checkData[$name];
    }

    /**
     * Get list
     * @param $name
     * @param $filters
     * @param VariableScope $scope
     * @return array
     * @throws Exception
     */
    public function getList($name, $filters, VariableScope $scope) {
        $lists = array(
            "attachment",
        );

        if (!in_array($name, $lists)) {
            return [];
        }

        $data = array();

        switch ($name) {
            case "attachment":
                foreach ($this->attachments as $attachment) {
                    if (in_array($attachment->type, array("image/jpeg", "image/png", "image/gif", "image/pjpeg", "text/plain"))) {
                        $data[] = $attachment;
                    }
                }

                break;
        }

        if ($filters) {
            foreach ($filters as $filter) {
                $filter = new ListFilter($filter, $scope);
                $data = $filter->apply($data);
            }
        }

        return $data;
    }

    /**
     * Get vuln overdued
     * @return bool
     */
    public function getVulnOverdued() {
        if (!$this->vuln_deadline) {
            return false;
        }

        if ($this->vuln_status == self::STATUS_VULN_RESOLVED) {
            return false;
        }

        $deadline = new DateTime($this->vuln_deadline . " 00:00:00");
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        if ($today > $deadline) {
            return true;
        }

        return false;
    }
}
