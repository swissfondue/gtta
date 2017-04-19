<?php

/**
 * This is the model class for table "projects".
 *
 * The followings are the available columns in table "projects":
 * @property integer $id
 * @property integer $client_id
 * @property string $year
 * @property string $name
 * @property string $deadline
 * @property integer $status
 * @property string $vuln_overdue
 * @property string $start_date
 * @property float $hours_allocated
 * @property int $report_template_id
 * @property bool $custom_report
 * @property string $report_options
 * @property float $userHoursAllocated
 * @property float $userHoursSpent
 * @property integer $language_id
 * @property Target[] $targets
 * @property ProjectReportSection[] $sections
 * @property string $import_filename
 */
class Project extends ActiveRecord implements IVariableScopeObject {
    /**
     * Project statuses.
     */
    const STATUS_OPEN = 0;
    const STATUS_IN_PROGRESS = 10;
    const STATUS_ON_HOLD = 20;
    const STATUS_FINISHED = 100;

    // sorting
    const FILTER_SORT_DEADLINE = 1;
    const FILTER_SORT_NAME = 2;
    const FILTER_SORT_CLIENT = 3;
    const FILTER_SORT_STATUS = 4;
    const FILTER_SORT_START_DATE = 5;

    // sorting direction
    const FILTER_SORT_ASCENDING = 1;
    const FILTER_SORT_DESCENDING = 2;

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "projects";
	}

    /**
     * Get valid statuses
     * @return array
     */
    public static function getValidStatuses() {
        return array(
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_ON_HOLD,
            self::STATUS_FINISHED
        );
    }

    /**
     * Get status titles
     * @return array
     */
    public static function getStatusTitles() {
        return [
            self::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
            self::STATUS_OPEN => Yii::t("app", "Open"),
            self::STATUS_IN_PROGRESS => Yii::t("app", "In Progress"),
            self::STATUS_FINISHED => Yii::t("app", "Finished"),
        ];
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["name, year", "required"],
            ["name", "length", "max" => 1000],
            ["year", "length", "max" => 4],
            ["status", "in", "range" => self::getValidStatuses()],
            ["hours_allocated", "numerical", "min" => 0],
            ["import_filename", "safe"],
		];
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"client" => array(self::BELONGS_TO, "Client", "client_id"),
            "details" => array(self::HAS_MANY, "ProjectDetail", "project_id"),
            "users" => array(self::MANY_MANY, "User", "project_users(project_id, user_id)"),
            "projectUsers" => array(self::HAS_MANY, "ProjectUser", "project_id"),
            "targets" => array(self::HAS_MANY, "Target", "project_id"),
            "userHoursAllocated" => array(self::STAT, "ProjectUser", "project_id", "select" => "SUM(hours_allocated)"),
            "trackedTime" => array(self::STAT, "ProjectTime", "project_id", "select" => "trunc(SUM(time) / 3600)"), // Convert seconds to hours
            "timeRecords" => array(self::HAS_MANY, "ProjectTime", "project_id"),
            "issues" => array(self::HAS_MANY, "Issue", "project_id"),
            "language" => array(self::BELONGS_TO, "Language", "language_id"),
            "sections" => array(self::HAS_MANY, "ProjectReportSection", "project_id"),
		);
	}

    /**
     * Check if user is permitted to access the project.
     */
    public function checkPermission() {
        $user = Yii::app()->user;

        if ($user->role == User::ROLE_ADMIN) {
            return true;
        }

        if (($user->role == User::ROLE_CLIENT && $user->client_id == $this->client_id) || $user->role == User::ROLE_USER) {
            $check = ProjectUser::model()->findByAttributes(array(
                "project_id" => $this->id,
                "user_id" => $user->id
            ));

            if ($check) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is project admin
     */
    public function checkAdmin() {
        if (User::checkRole(User::ROLE_ADMIN)) {
            return true;
        }

        if (User::checkRole(User::ROLE_USER)) {
            $check = ProjectUser::model()->findByAttributes(array(
                "project_id" => $this->id,
                "user_id" => Yii::app()->user->id,
            ));

            if ($check && $check->admin) {
                return true;
            }
        }

        return false;
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
            "year",
            "rating",
        );

        if (!in_array($name, $vars)) {
            return "";
        }

        if ($name == "rating") {
            return $scope->getStack()->getGlobal("rating");
        }

        return $this->$name;
    }

    /**
     * Get list
     * @param $name
     * @param array $filters
     * @param VariableScope $scope
     * @return array
     * @throws Exception
     */
    public function getList($name, $filters, VariableScope $scope) {
        $lists = array(
            "target",
            "detail",
            "category",
            "check",
        );

        if (!in_array($name, $lists)) {
            return [];
        }

        $data = array();

        switch ($name) {
            case "target":
                $data = $this->targets;
                break;

            case "detail":
                $data = $this->details;
                break;

            case "category":
                $targetIds = array();

                foreach ($this->targets as $target) {
                    $targetIds[] = $target->id;
                }

                $language = Language::model()->findByAttributes(array(
                    "code" => Yii::app()->language
                ));

                if ($language) {
                    $language = $language->id;
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition("target_id", $targetIds);
                $criteria->order = "l10n.name ASC";
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
                $targetIds = array();

                foreach ($this->targets as $target) {
                    $targetIds[] = $target->id;
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
                $criteria->addNotInCondition("t.rating", array(TargetCheck::RATING_HIDDEN));
                $criteria->together = true;

                $checks = TargetCustomCheck::model()->with(array("attachments"))->findAll($criteria);

                foreach ($checks as $check) {
                    $data[] = $check;
                }

                // regular checks
                $criteria = new CDbCriteria();
                $criteria->addInCondition("target_id", $targetIds);
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

    /**
     * Get user hours of project
     * @return int
     */
    public function getUserHoursSpent() {
        $records = ProjectTime::model()->findAllByAttributes(array(
            "project_id" => $this->id
        ));
        $hours = 0;

        foreach ($records as $record) {
            $hours += $record->hours;
        }

        return $hours;
    }

    /**
     * Check if field is hidden in project scope
     * @param $name
     */
    public function isFieldHidden($name) {
        $criteria = new CDbCriteria();
        $criteria->select = "g.name";
        $criteria->group = "g.name";
        $criteria->join = "INNER JOIN target_checks tc ON tc.id = t.target_check_id";
        $criteria->join .= sprintf(" INNER JOIN targets tr ON tr.id = tc.target_id AND tr.project_id = %d", $this->id);
        $criteria->join .= " INNER JOIN check_fields cf ON cf.id = t.check_field_id";
        $criteria->join .= sprintf(" INNER JOIN global_check_fields g ON g.id = cf.global_check_field_id AND g.name = '%s'", $name);
        $criteria->condition = "t.hidden IS true";

        return TargetCheckField::model()->count($criteria) > 0;
    }

    /**
     * Get project admin user
     * @return null
     */
    public function getAdmin() {
        foreach ($this->projectUsers as $user) {
            if ($user->admin) {
                return $user->user;
            }
        }

        return null;
    }

    /**
     * Returns project issues
     * @param int $offset
     * @return array|mixed|null
     */
    public function getIssues($offset = 0) {
        $language = System::model()->findByPk(1)->language;

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(["project_id" => $this->id]);
        $criteria->select = "t.id, cl10n.name, COUNT(DISTINCT tc.target_id) AS affected_targets, MAX(tc.rating) AS top_rating";
        $criteria->group = "t.id, cl10n.name";
        $criteria->join =
            "LEFT JOIN checks c ON c.id = t.check_id " .
            "LEFT JOIN checks_l10n cl10n ON cl10n.check_id = c.id " .
            "LEFT JOIN issue_evidences ie ON ie.issue_id = t.id " .
            "LEFT JOIN target_checks tc ON tc.id = ie.target_check_id";
        $criteria->addColumnCondition([
            "cl10n.language_id" => $language->id
        ]);

        if ($offset) {
            $criteria->offset = $offset;
        }

        $criteria->order = "top_rating DESC";

        return Issue::model()->findAll($criteria);
    }

    /**
     * Check if project has custom section specified
     * @param int $section
     * @return bool
     */
    public function hasSection($section) {
        foreach ($this->sections as $scn) {
            if ($scn->type == $section) {
                return true;
            }
        }

        return false;
    }
}
