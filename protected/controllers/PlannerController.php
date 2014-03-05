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
            "postOnly + data, control",
            "ajaxOnly + data, control",
        );
	}

    /**
     * Display the planner.
     */
	public function actionIndex() {
        if (isset($_POST["ProjectPlannerEditForm"])) {
            $form = new ProjectPlannerEditForm();
            $form->attributes = $_POST["ProjectPlannerEditForm"];

            if ($form->validate()) {
                $targetId = $form->targetId;
                $categoryId = $form->categoryId;
                $projectId = $form->projectId;
                $moduleId = $form->moduleId;
                $error = false;

                if ($targetId && $categoryId) {
                    $moduleId = null;

                    $plan = ProjectPlanner::model()->findByAttributes(array(
                        "target_id" => $targetId,
                        "check_category_id" => $categoryId
                    ));

                    if ($plan) {
                        Yii::app()->user->setFlash("error", Yii::t("app", "This category is already planned."));
                        $error = true;
                    }
                } else if ($projectId && $moduleId) {
                    $targetId = null;
                    $categoryId = null;

                    $plan = ProjectPlanner::model()->findByAttributes(array(
                        "project_id" => $projectId,
                        "gt_module_id" => $moduleId
                    ));

                    if ($plan) {
                        Yii::app()->user->setFlash("error", Yii::t("app", "This module is already planned."));
                        $error = true;
                    }
                } else {
                    Yii::app()->user->setFlash("error", Yii::t("app", "Please specify target and category or project and module."));
                    $error = true;
                }

                if (!$error) {
                    $plan = new ProjectPlanner();
                    $plan->start_date = $form->startDate;
                    $plan->end_date = $form->endDate;
                    $plan->user_id = $form->userId;
                    $plan->target_id = $targetId;
                    $plan->check_category_id = $categoryId;
                    $plan->project_id = $projectId;
                    $plan->gt_module_id = $moduleId;
                    $plan->save();
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
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
                        "finished" => round($plan->finished * 100) . "%"
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
    
    /**
     * Control function.
     */
    public function actionControl() {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST["EntryControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id = $model->id;
            $plan = ProjectPlanner::model()->findByPk($id);

            if ($plan === null) {
                throw new CHttpException(404, Yii::t("app", "Plan not found."));
            }

            switch ($model->operation) {
                case "delete":
                    $plan->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}
