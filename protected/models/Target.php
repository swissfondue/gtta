<?php

/**
 * This is the model class for table "targets".
 *
 * The followings are the available columns in table "targets":
 * @property integer $id
 * @property integer $project_id
 * @property string $host
 * @property boolean $ip
 * @property string $description
 * @property integer $port
 * @property integer $check_source_type
 * @property boolean $relation_template_id
 * @property boolean $relations
 * @property RelationTemplate $relationTemplate
 * @property TargetChecklistTemplate[] $checklistTemplates
 * @property TargetCheck[] $targetChecks
 */
class Target extends ActiveRecord implements IVariableScopeObject {
    const CHAIN_STATUS_IDLE = 0;
    const CHAIN_STATUS_ACTIVE = 1;
    const CHAIN_STATUS_STOPPED = 2;
    const CHAIN_STATUS_INTERRUPTED = 3;

    const SOURCE_TYPE_CHECK_CATEGORIES = 0;
    const SOURCE_TYPE_CHECKLIST_TEMPLATES = 1;

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
            array("project_id, check_source_type", "numerical", "integerOnly" => true),
            array("port, relations", "safe"),
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
            "checklistTemplates" => array(self::HAS_MANY, "TargetChecklistTemplate", "target_id"),
            "relationTemplate" => array(self::BELONGS_TO, "RelationTemplate", "relation_template_id"),
        );
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
            "port",
            "hostPort",
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

    /**
     * Check if target check can be duplicated (for targets with checklist templates)
     * @param $checkId
     * @return bool
     * @throws Exception
     */
    public function canAddCheck($checkId) {
        $availableCount = null;
        $currentCount = TargetCheck::model()->countByAttributes(array(
            "target_id" => $this->id,
            "check_id"  => $checkId,
        ));

        if ($this->check_source_type == Target::SOURCE_TYPE_CHECKLIST_TEMPLATES) {
            $templates = $this->checklistTemplates;
            $templateIds = array();

            foreach ($templates as $template) {
                $templateIds[] = $template->checklist_template_id;
            }

            $availableCount = ChecklistTemplateCheck::model()->countByAttributes(array(
                "checklist_template_id" => $templateIds,
                "check_id" => $checkId,
            ));
        } else {
            $availableCount = 1;
        }

        return $availableCount > $currentCount;
    }

    /**
     * Get host with port
     */
    public function getHostPort() {
        return $this->host . ($this->port ? ":" . $this->port : "");
    }

    /**
     * Check if check chain is running
     * @return bool
     */
    public function getIsChainRunning() {
        $job = JobManager::buildId(ChainJob::ID_TEMPLATE, array(
            "target_id" => $this->id,
            "operation" => ChainJob::OPERATION_START,
        ));

        return JobManager::isRunning($job);
    }
}
