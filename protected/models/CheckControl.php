<?php

/**
 * This is the model class for table "check_controls".
 * The followings are the available columns in table 'check_controls':
 * @property integer $id
 * @property integer $check_category_id
 * @property string $name
 * @property integer $sort_order
 * @property integer $external_id
 * @property integer $status
 * @property TargetCustomCheck[] $customChecks
 * @property CheckCategory category
 */
class CheckControl extends ActiveRecord implements IVariableScopeObject {
    const STATUS_INSTALLED = 1;
    const STATUS_SHARE = 2;

    // nearest sort order
    public $nearest_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckControl the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "check_controls";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name, check_category_id, sort_order", "required"),
            array("check_category_id, sort_order, external_id, status", "numerical", "integerOnly" => true),
            array("status", "in", "range" => array(
                self::STATUS_INSTALLED,
                self::STATUS_SHARE,
            )),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "l10n" => array(self::HAS_MANY, "CheckControlL10n", "check_control_id"),
            "checks" => array(self::HAS_MANY, "Check", "check_control_id"),
            "category" => array(self::BELONGS_TO, "CheckCategory", "check_category_id"),
            "checkCount" => array(self::STAT, "Check", "check_control_id"),
            "customChecks" => array(self::HAS_MANY, "TargetCustomCheck", "check_control_id"),
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
     * Get variable value
     * @param $name
     * @param VariableScope $scope
     * @return mixed
     * @throws Exception
     */
    public function getVariable($name, VariableScope $scope) {
        $vars = array(
            "name",
        );

        if (!in_array($name, $vars)) {
            return "";
        }

        return $this->$name;
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
            "check",
        );

        if (!in_array($name, $lists)) {
            return [];
        }

        $data = array();

        switch ($name) {
            case "check":
                $targetIds = array();

                try {
                    $targetScope = $scope->getStack()->get(VariableScope::SCOPE_TARGET);
                    $targetIds[] = $targetScope->getObject()->id;
                } catch (Exception $e) {
                    $projectScope = $scope->getStack()->get(VariableScope::SCOPE_PROJECT);
                    $targets = Target::model()->findAllByAttributes(array(
                        "project_id" => $projectScope->getObject()->id
                    ));

                    foreach ($targets as $target) {
                        $targetIds[] = $target->id;
                    }
                }

                $checkIds = array();
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array("check_control_id" => $this->id));
                $checks = Check::model()->findAll($criteria);

                foreach ($checks as $check) {
                    $checkIds[] = $check->id;
                }

                $language = Language::model()->findByAttributes(array(
                    "code" => Yii::app()->language
                ));

                if ($language) {
                    $language = $language->id;
                }

                // custom checks
                $criteria = new CDbCriteria();
                $criteria->addInCondition("t.target_id", $targetIds);
                $criteria->addColumnCondition(array("t.check_control_id" => $this->id));
                $criteria->addNotInCondition("t.rating", array(TargetCheck::RATING_HIDDEN));
                $criteria->together = true;

                $checks = TargetCustomCheck::model()->with(array("attachments"))->findAll($criteria);

                foreach ($checks as $check) {
                    $data[] = $check;
                }

                // regular checks
                $criteria = new CDbCriteria();
                $criteria->addInCondition("t.target_id", $targetIds);
                $criteria->addInCondition("t.check_id", $checkIds);
                $criteria->addColumnCondition(array("t.status" => TargetCheck::STATUS_FINISHED));
                $criteria->addNotInCondition("t.rating", array(TargetCheck::RATING_HIDDEN));
                $criteria->together = true;

                $checks = TargetCheck::model()->with(array(
                    "check" => array(
                        "with" => array(
                            "l10n" => array(
                                "joinType" => "LEFT JOIN",
                                "on" => "l10n.language_id = :language_id",
                                "params" => array("language_id" => $language)
                            ),
                            "_reference",
                        ),
                    ),

                    "solutions" => array(
                        "alias" => "tss",
                        "joinType" => "LEFT JOIN",
                        "with" => array(
                            "solution" => array(
                                "alias" => "tss_s",
                                "joinType" => "LEFT JOIN",
                                "with" => array(
                                    "l10n" => array(
                                        "alias" => "tss_s_l10n",
                                        "on" => "tss_s_l10n.language_id = :language_id",
                                        "params" => array("language_id" => $language)
                                    )
                                )
                            )
                        )
                    ),

                    "attachments",
                ))->findAll($criteria);

                foreach ($checks as $check) {
                    $data[] = $check;
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
}
