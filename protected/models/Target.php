<?php

/**
 * This is the model class for table "targets".
 *
 * The followings are the available columns in table "targets":
 * @property integer $id
 * @property integer $project_id
 * @property string $host
 * @property string $description
 * @property TargetCheck[] $targetChecks
 */
class Target extends ActiveRecord implements IVariableScopeObject {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Target the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "targets";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("host, project_id", "required"),
            array("host, description", "length", "max" => 1000),
            array("project_id", "numerical", "integerOnly" => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "_categories" => array(self::HAS_MANY, "TargetCheckCategory", "target_id"),
            "categories" => array(self::MANY_MANY, "CheckCategory", "target_check_categories(target_id, check_category_id)"),
            "_references" => array(self::HAS_MANY, "TargetReference", "target_id"),
            "references" => array(self::MANY_MANY, "Reference", "target_references(target_id, reference_id)"),
            "checkCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(check_count)"),
            "finishedCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(finished_count)"),
            "infoCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(info_count)"),
            "lowRiskCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(low_risk_count)"),
            "medRiskCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(med_risk_count)"),
            "highRiskCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(high_risk_count)"),
            "targetChecks" => array(self::HAS_MANY, "TargetCheck", "target_id"),
            "targetCustomChecks" => array(self::HAS_MANY, "TargetCustomCheck", "target_id"),
        );
	}

    /**
     * Synchronize target checks (delete old ones and create new).
     */
    public function syncChecks() {
        $checkIds = array();
        $referenceIds = array();

        $references = TargetReference::model()->findAllByAttributes(array(
            "target_id" => $this->id
        ));

        foreach ($references as $reference) {
            $referenceIds[] = $reference->reference_id;
        }

        $categories = TargetCheckCategory::model()->with("category")->findAllByAttributes(array(
            "target_id" => $this->id
        ));

        foreach ($categories as $category) {
            $controlIds = array();

            $controls = CheckControl::model()->findAllByAttributes(array(
                "check_category_id" => $category->check_category_id
            ));

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            $criteria = new CDbCriteria();

            if (!$category->advanced) {
                $criteria->addCondition("t.advanced = FALSE");
            }

            $criteria->addInCondition("t.check_control_id", $controlIds);
            $criteria->addInCondition("t.reference_id", $referenceIds);
            $checks = Check::model()->findAll($criteria);

            foreach ($checks as $check) {
                $checkIds[] = $check->id;
            }
        }

        // clean target checks
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition("check_id", $checkIds);
        $criteria->addColumnCondition(array(
            "target_id" => $this->id
        ));

        TargetCheck::model()->deleteAll($criteria);
        $cache = array();

        foreach (TargetCheck::model()->findAllByAttributes(array("target_id" => $this->id)) as $tc) {
            $cache[] = $tc->check_id;
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        foreach ($checkIds as $checkId) {
            if (in_array($checkId, $cache)) {
                continue;
            }

            $check = new TargetCheck();
            $check->target_id = $this->id;
            $check->check_id = $checkId;
            $check->status = TargetCheck::STATUS_OPEN;
            $check->rating = TargetCheck::RATING_NONE;
            $check->user_id = Yii::app()->user->id;
            $check->language_id = $language;
            $check->save();
        }
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
            "host",
            "description",
        );

        if (!in_array($name, $vars)) {
            throw new Exception(Yii::t("app", "Invalid variable: {var}.", array("{var}" => $name)));
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
            "category",
            "check",
        );

        if (!in_array($name, $lists)) {
            throw new Exception(Yii::t("app", "Invalid list: {list}.", array("{list}" => $name)));
        }

        $data = array();

        switch ($name) {
            case "category":
                $language = Language::model()->findByAttributes(array(
                    "code" => Yii::app()->language
                ));

                if ($language) {
                    $language = $language->id;
                }

                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array("target_id" => $this->id));
                $criteria->together = true;

                $targetCategories = TargetCheckCategory::model()->with(array(
                    "category" => array(
                        "with" => array(
                            "l10n" => array(
                                "joinType" => "LEFT JOIN",
                                "on" => "l10n.language_id = :language_id",
                                "params" => array("language_id" => $language)
                            ),
                        ),
                    ),
                ))->findAll($criteria);

                $categories = array();
                $ids = array();

                foreach ($targetCategories as $tc) {
                    if (in_array($tc->check_category_id, $ids)) {
                        continue;
                    }

                    $categories[] = $tc->category;
                    $ids[] = $tc->check_category_id;
                }

                $data = $categories;

                break;

            case "check":
                $language = Language::model()->findByAttributes(array(
                    "code" => Yii::app()->language
                ));

                if ($language) {
                    $language = $language->id;
                }

                // custom checks
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array("t.target_id" => $this->id));
                $criteria->addNotInCondition("t.rating", array(TargetCheck::RATING_HIDDEN));
                $criteria->together = true;

                $checks = TargetCustomCheck::model()->with(array("attachments"))->findAll($criteria);

                foreach ($checks as $check) {
                    $data[] = $check;
                }

                // regular checks
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array(
                    "t.target_id" => $this->id,
                    "t.status" => TargetCheck::STATUS_FINISHED,
                ));
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
