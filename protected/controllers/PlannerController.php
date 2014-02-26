<?php

/**
 * Project planner controller.
 */
class PlannerController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "checkAdmin",
            "postOnly + data, control, edit",
            "ajaxOnly + data, control, edit",
        );
	}

    /**
     * Display the planner.
     */
	public function actionIndex() {
        if (isset($_POST["ProjectPlannerEditForm"])) {
            $user_id = $_POST["ProjectPlannerEditForm"]["userId"];
            $target_id = $_POST["ProjectPlannerEditForm"]["targetId"];
            $category_id = $_POST["ProjectPlannerEditForm"]["categoryId"];
            $start = $_POST["ProjectPlannerEditForm"]["startDate"];
            $end = $_POST["ProjectPlannerEditForm"]["endDate"];

            $plan = new ProjectPlanner();
            $plan->start_date = $start;
            $plan->end_date = $end;
            $plan->user_id = $user_id;
            $plan->target_id = $target_id;
            $plan->check_category_id = $category_id;
            $plan->save();
        }

        $criteria = new CDbCriteria();
        $criteria->order = "t.role ASC, t.name ASC, t.email ASC";
        $criteria->addInCondition("t.role", array(User::ROLE_ADMIN, User::ROLE_USER));
        $criteria->together = true;

        $users = User::model()->findAll($criteria);

        $criteria = new CDbCriteria();
        $criteria->order = "t.name ASC";
        $clients = Client::model()->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t("app", "Project Planner"), "");
        $this->pageTitle = Yii::t("app", "Project Planner");
		$this->render("index", array(
            "users" => $users,
            "clients" => $clients,
        ));
	}

    /**
     * Load planner data.
     */
    public function actionData() {
        $response = new AjaxResponse();

        try {
            $model = new ProjectPlannerLoadForm();
            $model->attributes = $_POST["ProjectPlannerLoadForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $language = Language::model()->findByAttributes(array(
                "code" => Yii::app()->language
            ));

            if ($language) {
                $language = $language->id;
            }

            $userList = array();

            $criteria = new CDbCriteria();
            $criteria->order = "t.role ASC, t.name ASC, t.email ASC";
            $criteria->addInCondition("t.role", array(User::ROLE_ADMIN, User::ROLE_USER));
            $criteria->together = true;

            $users = User::model()->with(array(
                "planner" => array(
                    "joinType" => "LEFT JOIN",
                    "on" => "(planner.start_date, planner.end_date) OVERLAPS (:start_date::date, :end_date::date)",
                    "params" => array(
                        "start_date" => $model->startDate,
                        "end_date" => $model->endDate,
                    ),
                    "with" => array(
                        "targetCheckCategory" => array(
                            "with" => array(
                                "target" => array(
                                    "with" => array(
                                        "project"
                                    )
                                ),
                                "category" => array(
                                    "with" => array(
                                        "l10n" => array(
                                            "joinType" => "LEFT JOIN",
                                            "on" => "l10n.language_id = :language_id",
                                            "params" => array(
                                                "language_id" => $language,
                                            ),
                                        ),
                                    )
                                )
                            )
                        ),
                        "projectGtModule" => array(
                            "with" => array(
                                "project" => array(
                                    "alias" => "gt_project"
                                ),
                                "module" => array(
                                    "with" => array(
                                        "l10n" => array(
                                            "alias" => "l10n_module",
                                            "joinType" => "LEFT JOIN",
                                            "on" => "l10n_module.language_id = :language_id",
                                            "params" => array(
                                                "language_id" => $language,
                                            ),
                                        ),
                                    )
                                )
                            )
                        ),
                    )
                )
            ))->findAll($criteria);

            foreach ($users as $user) {
                $plans = array();

                /** @var ProjectPlanner $plan */
                foreach ($user->planner as $plan) {
                    $name = null;
                    $link = null;

                    if ($plan->targetCheckCategory) {
                        $name = implode(", ", array(
                            $plan->targetCheckCategory->category->localizedName,
                            $plan->targetCheckCategory->target->host,
                            $plan->targetCheckCategory->target->project->name,
                        ));

                        $link = $this->createUrl("project/checks", array(
                            "id" => $plan->targetCheckCategory->target->project_id,
                            "target" => $plan->targetCheckCategory->target_id,
                            "category" => $plan->targetCheckCategory->check_category_id,
                        ));
                    } else {
                        $name = implode(", ", array(
                            $plan->projectGtModule->module->localizedName,
                            $plan->projectGtModule->project->name,
                        ));

                        $link = $this->createUrl("project/gt", array("id" => $plan->project_id));
                    }

                    $plans[] = array(
                        "id" => $plan->id,
                        "name" => $name,
                        "link" => $link,
                        "startDate" => $plan->start_date,
                        "endDate" => $plan->end_date,
                        "finished" => $plan->finished
                    );
                }

                $userList[] = array(
                    "id" => $user->id,
                    "name" => $user->name ? $user->name : $user->email,
                    "link" => $this->createUrl("user/edit", array("id" => $user->id)),
                    "data" => $plans,
                );
            }

            $response->addData("users", $userList);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}
