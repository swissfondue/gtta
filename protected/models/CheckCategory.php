<?php

/**
 * This is the model class for table "check_categories".
 *
 * The followings are the available columns in table "check_categories":
 * @property integer $id
 * @property string $name
 * @property integer $external_id
 * @property integer $status
 * @property CheckControl[] controls
 */
class CheckCategory extends ActiveRecord implements IVariableScopeObject {
    const STATUS_INSTALLED = 1;
    const STATUS_SHARE = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckCategory the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "check_categories";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name", "required"),
            array("external_id, status", "numerical", "integerOnly" => true),
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
            "l10n" => array(self::HAS_MANY, "CheckCategoryL10n", "check_category_id"),
            "controls" => array(self::HAS_MANY, "CheckControl", "check_category_id"),
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
            "control",
            "check",
        );

        if (!in_array($name, $lists)) {
            return [];
        }

        $data = array();

        switch ($name) {
            case "control":
                $language = Language::model()->findByAttributes(array(
                    "code" => Yii::app()->language
                ));

                if ($language) {
                    $language = $language->id;
                }

                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array("check_category_id" => $this->id));
                $criteria->together = true;

                $controls = CheckControl::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "l10n.language_id = :language_id",
                        "params" => array("language_id" => $language)
                    ),
                ))->findAll($criteria);

                $data = $controls;

                break;

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

                $controlIds = array();
                $controls = CheckControl::model()->findAllByAttributes(array("check_category_id" => $this->id));

                foreach ($controls as $control) {
                    $controlIds[] = $control->id;
                }

                $checkIds = array();

                $criteria = new CDbCriteria();
                $criteria->addInCondition("check_control_id", $controlIds);
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
                $criteria->addInCondition("target_id", $targetIds);
                $criteria->addInCondition("check_control_id", $controlIds);
                $criteria->addNotInCondition("t.rating", array(TargetCheck::RATING_HIDDEN));
                $criteria->together = true;

                $checks = TargetCustomCheck::model()->with(array("attachments"))->findAll($criteria);

                foreach ($checks as $check) {
                    $data[] = $check;
                }

                // regular checks
                $criteria = new CDbCriteria();
                $criteria->addInCondition("target_id", $targetIds);
                $criteria->addInCondition("check_id", $checkIds);
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
