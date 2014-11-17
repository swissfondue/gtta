<?php

/**
 * Project controller.
 */
class ProjectController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "showDetails + target, attachment, checks",
            "checkUser + control, edittarget, controltarget, uploadattachment, controlattachment, controlcheck, updatechecks, gtcontrolcheck, gtsavecheck, savecheck, gtupdatechecks, gtuploadattachment, gtcontrolattachment, copycheck, time, tracktime, controlcategory",
            "checkAdmin + edit, users, edituser, controluser, controltime",
            "ajaxOnly + savecheck, savecustomcheck, controlattachment, controlcheck, updatechecks, controluser, gtcontrolcheck, gtsavecheck, gtupdatechecks, gtcontrolattachment, copycheck, controlchecklist, check, controlcategory",
            "postOnly + savecheck, savecustomcheck, uploadattachment, controlattachment, controlcheck, updatechecks, controluser, gtcontrolcheck, gtsavecheck, gtupdatechecks, gtuploadattachment, gtcontrolattachment, copycheck, controlchecklist, check, controlcategory",
            "idleOrRunning",
		);
	}

    /**
     * Display a list of projects.
     */
	public function actionIndex($page=1) {
        $page = (int) $page;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $cookies = Yii::app()->request->cookies;
        $filtered = array();

        if (isset($cookies["project_filter_status"]) && strlen($cookies["project_filter_status"]->value)) {
            $statusCookie = $cookies["project_filter_status"]->value;
            $statuses = explode(",", $statusCookie);

            foreach ($statuses as $s) {
                if (in_array((int) $s, Project::getValidStatuses())) {
                    $filtered[] = (int) $s;
                }
            }
        }

        if (!$filtered) {
            $filtered = array(Project::STATUS_OPEN, Project::STATUS_IN_PROGRESS);
        }

        $showStatuses = $filtered;
        $sortBy = null;
        $sortDirection = null;

        $sortByCookie = isset($cookies["project_filter_sort_by"]) ?
            (int) $cookies["project_filter_sort_by"]->value : Project::FILTER_SORT_DEADLINE;
        $sortDirectionCookie = isset($cookies["project_filter_sort_direction"]) ?
            (int) $cookies["project_filter_sort_direction"]->value : Project::FILTER_SORT_ASCENDING;

        switch ($sortDirectionCookie) {
            case Project::FILTER_SORT_ASCENDING:
                $sortDirection = "ASC";
                break;

            case Project::FILTER_SORT_DESCENDING:
                $sortDirection = "DESC";
                break;
        }

        switch ($sortByCookie) {
            case Project::FILTER_SORT_DEADLINE:
                $sortBy = "t.deadline";
                break;

            case Project::FILTER_SORT_NAME:
                $sortBy = "t.name";
                break;

            case Project::FILTER_SORT_CLIENT:
                $sortBy = "client.name";
                break;

            case Project::FILTER_SORT_STATUS:
                $sortBy = "t.status";
                break;

            case Project::FILTER_SORT_START_DATE:
                $sortBy = "t.start_date";
                break;
        }

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params["entriesPerPage"];
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];
        $criteria->together = true;
        $criteria->addInCondition("status", $showStatuses);

        if ($sortBy && $sortDirection) {
            $criteria->order = $sortBy . " " . $sortDirection . ", t.name ASC";
        }

        if (User::checkRole(User::ROLE_CLIENT)) {
            $user = User::model()->findByPk(Yii::app()->user->id);
            $criteria->addColumnCondition(array("client_id" => $user->client_id));
        }

        if (User::checkRole(User::ROLE_ADMIN)) {
            $projects = Project::model()->with("client")->findAll($criteria);
            $projectCount = Project::model()->count($criteria);
        } else {
            $projects = Project::model()->with(array(
                "projectUsers" => array(
                    "joinType" => "INNER JOIN",
                    "on" => "user_id = :user_id",
                    "params" => array(
                        "user_id" => Yii::app()->user->id,
                    ),
                ),
                "client"
            ))->findAll($criteria);

            $projectCount = Project::model()->with(array(
                "projectUsers" => array(
                    "joinType" => "INNER JOIN",
                    "on" => "user_id = :user_id",
                    "params" => array(
                        "user_id" => Yii::app()->user->id,
                    ),
                ),
                "client"
            ))->count($criteria);
        }

        $paginator = new Paginator($projectCount, $page);
        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), "");
        $projectStats = array();

        foreach ($projects as $project) {
            $checkCount    = 0;
            $finishedCount = 0;
            $lowRiskCount  = 0;
            $medRiskCount  = 0;
            $highRiskCount = 0;

            if ($project->guided_test) {
                $checks = ProjectGtCheck::model()->findAllByAttributes(array(
                    'project_id' => $project->id
                ));

                foreach ($checks as $check) {
                    $checkCount++;

                    if ($check->status == ProjectGtCheck::STATUS_FINISHED) {
                        $finishedCount++;
                    }

                    switch ($check->rating) {
                        case ProjectGtCheck::RATING_LOW_RISK:
                            $lowRiskCount++;
                            break;

                        case ProjectGtCheck::RATING_MED_RISK:
                            $medRiskCount++;
                            break;

                        case ProjectGtCheck::RATING_HIGH_RISK:
                            $highRiskCount++;
                            break;

                        default:
                            break;
                    }
                }
            } else {
                $targets = Target::model()->with(array(
                    'checkCount',
                    'finishedCount',
                    'lowRiskCount',
                    'medRiskCount',
                    'highRiskCount',
                ))->findAllByAttributes(array(
                    'project_id' => $project->id
                ));

                foreach ($targets as $target) {
                    if ($target->checkCount)
                        $checkCount += $target->checkCount;

                    if ($target->finishedCount)
                        $finishedCount += $target->finishedCount;

                    if ($target->lowRiskCount)
                        $lowRiskCount += $target->lowRiskCount;

                    if ($target->medRiskCount)
                        $medRiskCount += $target->medRiskCount;

                    if ($target->highRiskCount)
                        $highRiskCount += $target->highRiskCount;
                }
            }

            $projectStats[$project->id] = array(
                'checkCount'    => $checkCount,
                'finishedCount' => $finishedCount,
                'lowRiskCount'  => $lowRiskCount,
                'medRiskCount'  => $medRiskCount,
                'highRiskCount' => $highRiskCount
            );
        }

        // display the page
        $this->pageTitle = Yii::t("app", "Projects");
		$this->render("index", array(
            "projects" => $projects,
            "stats" => $projectStats,
            "p" => $paginator,
            "showStatuses" => $showStatuses,
            "sortBy" => $sortByCookie,
            "sortDirection" => $sortDirectionCookie,
            "statuses" => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN => Yii::t("app", "Open"),
                Project::STATUS_IN_PROGRESS => Yii::t("app", "In Progress"),
                Project::STATUS_FINISHED => Yii::t("app", "Finished"),
            )
        ));
	}

    /**
     * Display a standard project page
     */
    private function _viewStandard($project, $page) {
        $client = Client::model()->findByPk($project->client_id);

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.host ASC';
        $criteria->addCondition('t.project_id = :project_id');
        $criteria->params = array( 'project_id' => $project->id );
        $criteria->together = true;

        $targets = Target::model()->findAll($criteria);
        $targetIds = array();

        foreach ($targets as $target) {
            $targetIds[] = $target->id;
        }

        $newCriteria = new CDbCriteria();
        $newCriteria->order  = 't.host ASC';
        $newCriteria->addCondition('t.project_id = :project_id');
        $newCriteria->params = array( 'project_id' => $project->id );
        $newCriteria->addInCondition('t.id', $targetIds);
        $newCriteria->together = true;

        $targets = Target::model()->with(array(
            'checkCount',
            'finishedCount',
            'lowRiskCount',
            'medRiskCount',
            'highRiskCount',
            'categories' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    ),
                    'controls' => array(
                        'with' => 'checkCount'
                    )
                ),
                'order' => 'categories.name',
            )
        ))->findAll($newCriteria);

        $targetCount = Target::model()->count($criteria);
        $paginator = new Paginator($targetCount, $page);

        $criteria = new CDbCriteria();
        $criteria->order  = "t.host ASC";
        $criteria->addCondition("t.project_id = :project_id");
        $criteria->params = array("project_id" => $project->id);
        $criteria->together = true;

        $quickTargets = Target::model()->with(array(
            "categories" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    ),
                ),
                "order" => "categories.name",
            )
        ))->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array($project->name, '');

        // display the page
        $this->pageTitle = $project->name;
		$this->render('view', array(
            "project"  => $project,
            "client"   => $client,
            "targets"  => $targets,
            "p"        => $paginator,
            "statuses" => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN        => Yii::t("app", "Open"),
                Project::STATUS_IN_PROGRESS => Yii::t("app", "In Progress"),
                Project::STATUS_FINISHED    => Yii::t("app", "Finished"),
            ),
            "ratings" => TargetCheck::getRatingNames(),
            "columns" => array(
                TargetCheck::COLUMN_TARGET          => Yii::t("app", "Target"),
                TargetCheck::COLUMN_NAME            => Yii::t("app", "Name"),
                TargetCheck::COLUMN_REFERENCE       => Yii::t("app", "Reference"),
                TargetCheck::COLUMN_BACKGROUND_INFO => Yii::t("app", "Background Info"),
                TargetCheck::COLUMN_QUESTION        => Yii::t("app", "Question"),
                TargetCheck::COLUMN_RESULT          => Yii::t("app", "Result"),
                TargetCheck::COLUMN_SOLUTION        => Yii::t("app", "Solution"),
                TargetCheck::COLUMN_RATING          => Yii::t("app", "Rating"),
            ),
            "quickTargets" => $quickTargets,
        ));
    }

    /**
     * Get GT check.
     */
    private function _getGtStep($project, $step) {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('project_id' => $project->id));
        $criteria->order = "t.sort_order ASC";
        $criteria->together = true;

        $modules = ProjectGtModule::model()->with(array(
            'module' => array(
                'with' => array(
                    'checks' => array(
                        'order' => 'checks.sort_order ASC'
                    )
                )
            )
        ))->findAll($criteria);

        $currentStep = null;

        if ($step >= 0) {
            foreach ($modules as $module) {
                foreach ($module->module->checks as $check) {
                    if ($step == 0) {
                        $currentStep = array($module, $check);
                        $step--;

                        break;
                    }

                    $step--;

                    if ($step < 0) {
                        break;
                    }
                }

                if ($step < 0) {
                    break;
                }
            }
        }

        return $currentStep;
    }

    /**
     * Display a guided test-enabled project page
     */
    private function _viewGuidedTest($project) {
        $client = Client::model()->findByPk($project->client_id);

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $modules = ProjectGtModule::model()->findAllByAttributes(array('project_id' => $project->id));
        $moduleIds = array();

        foreach ($modules as $module) {
            $moduleIds[] = $module->gt_module_id;
        }

        $model = new ProjectGtForm();

        if (isset($_POST["ProjectGtForm"])) {
            $model->attributes = $_POST['ProjectGtForm'];

			if ($model->validate()) {
                $moduleIds = array();
                $oldModuleIds = array();

                $modules = ProjectGtModule::model()->findAllByAttributes(array(
                    'project_id' => $project->id
                ));

                foreach ($modules as $module) {
                    $oldModuleIds[] = $module->gt_module_id;
                }

                if ($model->modules) {
                    foreach ($model->modules as $moduleId => $value) {
                        $moduleIds[] = $moduleId;
                    }
                }

                $newModuleIds = array_diff($moduleIds, $oldModuleIds);
                $delModuleIds = array_diff($oldModuleIds, $moduleIds);

                // delete old modules
                $criteria = new CDbCriteria();
                $criteria->addInCondition('gt_module_id', $delModuleIds);
                $checks = GtCheck::model()->findAll($criteria);

                $checkIds = array();

                foreach ($checks as $check) {
                    $checkIds[] = $check->id;
                }

                $checkCriteria = new CDbCriteria();
                $checkCriteria->addInCondition('gt_check_id', $checkIds);
                $checkCriteria->addColumnCondition(array('project_id' => $project->id));

                ProjectGtCheckAttachment::model()->deleteAll($checkCriteria);
                ProjectGtCheckInput::model()->deleteAll($checkCriteria);
                ProjectGtCheckSolution::model()->deleteAll($checkCriteria);
                ProjectGtCheck::model()->deleteAll($checkCriteria);

                $criteria->addColumnCondition(array('project_id' => $project->id));
                ProjectGtModule::model()->deleteAll($criteria);

                $criteria = new CDbCriteria();
                $criteria->select = 'MAX(sort_order) as max_sort_order';
                $criteria->addColumnCondition(array('project_id' => $project->id));

                $maxOrder = ProjectGtModule::model()->find($criteria);
                $sortOrder = 0;

                if ($maxOrder && $maxOrder->max_sort_order !== null) {
                    $sortOrder = $maxOrder->max_sort_order + 1;
                }

                // create new ones
                foreach ($newModuleIds as $id) {
                    $module = new ProjectGtModule();
                    $module->project_id = $project->id;
                    $module->gt_module_id = $id;
                    $module->sort_order = $sortOrder;
                    $module->save();

                    $sortOrder++;
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Project saved.'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 'COALESCE(l10n.name, t.name) ASC';;
        $criteria->together = true;

        $categories = GtCategory::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            ),
            "types" => array(
                "with" => array(
                    "l10n" => array(
                        "alias" => "l10n_t",
                        "joinType" => "LEFT JOIN",
                        "on" => "l10n_t.language_id = :language_id",
                        "params" => array("language_id" => $language),
                    ),
                    "modules" => array(
                        "with" => array(
                            "l10n" => array(
                                "alias" => "l10n_m",
                                "joinType" => "LEFT JOIN",
                                "on" => "l10n_m.language_id = :language_id",
                                "params" => array("language_id" => $language),
                            )
                        ),
                        "order" => "COALESCE(l10n_m.name, modules.name) ASC",
                    )
                ),
                "order" => "COALESCE(l10n_t.name, types.name) ASC",
            )
        ))->findAll($criteria);

        $nextStep = $this->_getGtStep($project, 0);
        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, "");

        // display the page
        $this->pageTitle = $project->name;
		$this->render("gt/index", array(
            "project" => $project,
            "client" => $client,
            "categories" => $categories,
            "statuses" => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN => Yii::t("app", "Open"),
                Project::STATUS_IN_PROGRESS => Yii::t("app", "In Progress"),
                Project::STATUS_FINISHED => Yii::t("app", "Finished"),
            ),
            "modules" => $moduleIds,
            "nextStep" => $nextStep,
        ));
    }

    /**
     * Display a list of targets.
     */
	public function actionView($id, $page=1) {
        $id = (int) $id;
        $page = (int) $page;

        $project = Project::model()->with(array(
            "details" => array(
                "order" => "subject ASC"
            ),
            "userHoursAllocated",
            "userHoursSpent",
        ))->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));
        }

        if (!$project->checkPermission()) {
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        if ($project->guided_test) {
            $this->_viewGuidedTest($project);
        } else {
            $this->_viewStandard($project, $page);
        }
	}

    /**
     * Project edit page.
     */
	public function actionEdit($id=0) {
        $id = (int) $id;
        $newRecord = false;

        if ($id) {
            $project = Project::model()->findByPk($id);
        } else {
            $project = new Project();
            $newRecord = true;
        }

		$model = new ProjectEditForm(
            User::checkRole(User::ROLE_ADMIN) ? ProjectEditForm::ADMIN_SCENARIO : ProjectEditForm::USER_SCENARIO,
            $id
        );

        if (!$newRecord) {
            $model->name = $project->name;
            $model->year = $project->year;
            $model->status = $project->status;
            $model->clientId = $project->client_id;
            $model->deadline = $project->deadline;
            $model->startDate = $project->start_date ? $project->start_date : date("Y-m-d");
            $model->hoursAllocated = $project->hours_allocated;
        } else {
            $model->year = date("Y");
            $model->deadline = date("Y-m-d");
            $model->startDate = date("Y-m-d");
        }

		// collect user input data
		if (isset($_POST["ProjectEditForm"])) {
			$model->attributes = $_POST["ProjectEditForm"];

			if ($model->validate()) {
                // delete all client accounts from this project
                if (!$newRecord && $model->clientId != $project->client_id) {
                    $clientUsers = User::model()->findAllByAttributes(array(
                        "role" => User::ROLE_CLIENT,
                        "client_id" => $project->client_id
                    ));

                    $clientUserIds = array();

                    foreach ($clientUsers as $user)
                        $clientUserIds[] = $user->id;

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("user_id", $clientUserIds);
                    $criteria->addColumnCondition(array(
                        "project_id" => $project->id
                    ));

                    ProjectUser::model()->deleteAll($criteria);
                }

                $project->name = $model->name;
                $project->year = $model->year;
                $project->status = $model->status;
                $project->client_id = $model->clientId;
                $project->start_date = $model->startDate;
                $project->deadline = $model->deadline;
                $project->hours_allocated = $model->hoursAllocated;
                $project->save();

                if ($newRecord) {
                    $projectUser = new ProjectUser();
                    $projectUser->user_id = Yii::app()->user->id;
                    $projectUser->project_id = $project->id;
                    $projectUser->admin = true;
                    $projectUser->save();
                }

                Yii::app()->user->setFlash("success", Yii::t("app", "Project saved."));

                $project->refresh();

                if ($newRecord) {
                    $this->redirect(array("project/edit", "id" => $project->id));
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t("app", "New Project"), "");
        } else {
            $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
            $this->breadcrumbs[] = array(Yii::t("app", "Edit"), "");
        }

        $clients = Client::model()->findAllByAttributes(
            array(),
            array("order" => "t.name ASC")
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t("app", "New Project") : $project->name;
		$this->render("edit", array(
            "model" => $model,
            "project" => $project,
            "clients" => $clients,
            "statuses" => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN => Yii::t("app", "Open"),
                Project::STATUS_IN_PROGRESS => Yii::t("app", "In Progress"),
                Project::STATUS_FINISHED => Yii::t("app", "Finished"),
            )
        ));
	}

    /**
     * Display a list of details.
     */
	public function actionDetails($id, $page=1)
	{
        $id   = (int) $id;
        $page = (int) $page;

        if (!User::checkRole(User::ROLE_ADMIN) && !User::checkRole(User::ROLE_CLIENT))
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if (!$project->checkPermission())
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.subject ASC';
        $criteria->addCondition('t.project_id = :project_id');
        $criteria->params = array( 'project_id' => $project->id );

        $details = ProjectDetail::model()->findAll($criteria);

        $detailCount = ProjectDetail::model()->count($criteria);
        $paginator   = new Paginator($detailCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array($project->name, $this->createUrl('project/view', array( 'id' => $project->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Details'), '');

        // display the page
        $this->pageTitle = $project->name;
		$this->render('detail/index', array(
            'project'  => $project,
            'details'  => $details,
            'p'        => $paginator,
        ));
	}

    /**
     * Project detail edit page.
     */
	public function actionEditDetail($id, $detail=0)
	{
        $id        = (int) $id;
        $detail    = (int) $detail;
        $newRecord = false;

        if (!User::checkRole(User::ROLE_ADMIN) && !User::checkRole(User::ROLE_CLIENT))
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if (!$project->checkPermission())
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        if ($detail)
        {
            $detail = ProjectDetail::model()->findByAttributes(array(
                'id'         => $detail,
                'project_id' => $project->id
            ));

            if (!$detail)
                throw new CHttpException(404, Yii::t('app', 'Detail not found.'));
        }
        else
        {
            $detail   = new ProjectDetail();
            $newRecord = true;
        }

		$model = new ProjectDetailEditForm();

        if (!$newRecord)
        {
            $model->subject = $detail->subject;
            $model->content = $detail->content;
        }

		// collect user input data
		if (isset($_POST['ProjectDetailEditForm']))
		{
			$model->attributes = $_POST['ProjectDetailEditForm'];

			if ($model->validate())
            {
                $detail->project_id = $project->id;
                $detail->subject    = $model->subject;
                $detail->content    = $model->content;

                $detail->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Detail saved.'));

                $detail->refresh();

                if ($newRecord)
                    $this->redirect(array( 'project/editdetail', 'id' => $project->id, 'detail' => $detail->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array($project->name, $this->createUrl('project/view', array( 'id' => $project->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Details'), $this->createUrl('project/details', array( 'id' => $project->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Detail'), '');
        else
            $this->breadcrumbs[] = array($detail->subject, '');

		// display the page
        $this->pageTitle = $detail->isNewRecord ? Yii::t('app', 'New Detail') : $detail->subject;
		$this->render('detail/edit', array(
            'model'   => $model,
            'project' => $project,
            'detail'  => $detail
        ));
	}

    /**
     * Display list of tracked time
     * @param $id
     * @param int $page
     */
    public function actionTime($id, $page=1) {
        $id   = (int) $id;
        $page = (int) $page;

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if (!$project->checkPermission())
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.create_time ASC';
        $criteria->addCondition('t.project_id = :project_id');
        $criteria->params = array( 'project_id' => $project->id );

        $records = ProjectTime::model()->findAll($criteria);

        $detailCount = ProjectTime::model()->count($criteria);
        $paginator   = new Paginator($detailCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array($project->name, $this->createUrl('project/view', array( 'id' => $project->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Time'), '');

        // display the page
        $this->pageTitle = $project->name;
        $this->render('time/index', array(
            'project'  => $project,
            'records'  => $records,
            'p'        => $paginator,
        ));
    }

    /**
     * Control time function
     */
    public function actionControlTime() {
        $response = new AjaxResponse();

        try
        {
            $form = new EntryControlForm();
            $form->attributes = $_POST['EntryControlForm'];

            if (!$form->validate())
            {
                $errorText = '';

                foreach ($form->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id     = $form->id;
            $record = ProjectTime::model()->with('project')->findByPk($id);

            if ($record === null)
                throw new CHttpException(404, Yii::t('app', 'Record not found.'));

            if (!$record->project->checkPermission())
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));

            switch ($form->operation)
            {
                case 'delete':
                    $record->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Check form for GT.
     */
    public function actionGt($id) {
        $id = (int) $id;
        $project = Project::model()->with(array(
            "userHoursAllocated",
            "userHoursSpent",
        ))->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));
        }

        if (!$project->guided_test || !$project->checkPermission()) {
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $cookies = Yii::app()->request->cookies;
        $step = isset($cookies['gt_step']) ? $cookies['gt_step']->value : 0;
        $stepObject = $this->_getGtStep($project, $step);

        if (!$stepObject) {
            $step = 0;
            $stepObject = $this->_getGtStep($project, 0);

            if (!$stepObject) {
                throw new CHttpException(404, Yii::t('app', 'Step not found.'));
            }
        }

        list($module, $check) = $stepObject;

        $module = ProjectGtModule::model()->with(array(
            'module' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on' => 'language_id = :language_id',
                        'params' => array('language_id' => $language)
                    ),
                    'suggestedTargets' => array(
                        'alias' => 'st',
                        'on' => 'st.project_id = :project_id AND st.approved',
                        'params' => array('project_id' => $project->id),
                        'with' => array(
                            'check' => array(
                                'alias' => 'gt_check',
                                'with' => array(
                                    'check' => array(
                                        'with' => array(
                                            'l10n' => array(
                                                'alias' => 'l10n_cc',
                                                'joinType' => 'LEFT JOIN',
                                                'on' => 'l10n_cc.language_id = :language_id',
                                                'params' => array('language_id' => $language)
                                            ),
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        ))->findByAttributes(array(
            'project_id' => $project->id,
            'gt_module_id' => $module->gt_module_id
        ));

        if (!$module) {
            throw new CHttpException(404, Yii::t('app', 'Module not found.'));
        }

        $check = GtCheck::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on' => 'language_id = :language_id',
                'params' => array('language_id' => $language)
            ),
            'check' => array(
                'with' => array(
                    '_reference',
                    'l10n' => array(
                        'alias' => 'l10n_c',
                        'joinType' => 'LEFT JOIN',
                        'on' => 'l10n_c.language_id = :language_id',
                        'params' => array('language_id' => $language)
                    ),
                    'scripts' => array(
                        'with' => array(
                            'inputs' => array(
                                "on" => "inputs.visible AND inputs.check_script_id = scripts.id",
                                'with' => array(
                                    'l10n' => array(
                                        'alias' => 'l10n_i',
                                        'joinType' => 'LEFT JOIN',
                                        'on' => 'l10n_i.language_id = :language_id',
                                        'params' => array('language_id' => $language)
                                    ),
                                    'projectInputs' => array(
                                        'joinType' => 'LEFT JOIN',
                                        'on' => '"projectInputs".project_id = :project_id AND "projectInputs".gt_check_id = :check_id',
                                        'params' => array('project_id' => $project->id, 'check_id' => $check->id)
                                    ),
                                ),
                                'order' => 'inputs.sort_order ASC',
                            )
                        )
                    ),
                    'results' => array(
                        'with' => array(
                            'l10n' => array(
                                'alias' => 'l10n_r',
                                'joinType' => 'LEFT JOIN',
                                'on' => 'l10n_r.language_id = :language_id',
                                'params' => array('language_id' => $language)
                            ),
                        ),
                        'order' => 'results.sort_order ASC'
                    ),
                    'solutions' => array(
                        'with' => array(
                            'l10n' => array(
                                'alias' => 'l10n_s',
                                'joinType' => 'LEFT JOIN',
                                'on' => 'l10n_s.language_id = :language_id',
                                'params' => array('language_id' => $language)
                            ),
                        ),
                        'order' => 'solutions.sort_order ASC'
                    ),
                )
            ),
            'projectChecks' => array(
                'alias' => 'pcs',
                'joinType' => 'LEFT JOIN',
                'on' => 'pcs.project_id = :project_id',
                'params' => array('project_id' => $project->id)
            ),
            'projectCheckSolutions' => array(
                'alias' => 'pss',
                'joinType' => 'LEFT JOIN',
                'on' => 'pss.project_id = :project_id',
                'params' => array('project_id' => $project->id)
            ),
            'projectCheckAttachments' => array(
                'alias' => 'pas',
                'joinType' => 'LEFT JOIN',
                'on' => 'pas.project_id = :project_id',
                'params' => array('project_id' => $project->id),
            ),
            'suggestedTargets' => array(
                'alias' => 'sgt',
                'joinType' => 'LEFT JOIN',
                'on' => 'sgt.project_id = :project_id',
                'params' => array('project_id' => $project->id),
                'with' => array(
                    'module' => array(
                        'with' => array(
                            'l10n' => array(
                                'alias' => 'l10n_sgt_m',
                                'joinType' => 'LEFT JOIN',
                                'on' => 'l10n_sgt_m.language_id = :language_id',
                                'params' => array('language_id' => $language)
                            ),
                        )
                    )
                )
            ),
        ))->findByAttributes(array(
            'id' => $check->id,
            'gt_module_id' => $module->gt_module_id
        ));

        if (!$check) {
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));
        }

        $modules = ProjectGtModule::model()->with(array(
            'module' => array(
                'with' => 'checkCount'
            )
        ))->findAllByAttributes(array(
            'project_id' => $project->id
        ));

        $checkCount = 0;

        foreach ($modules as $mod) {
            $checkCount += $mod->module->checkCount;
        }

        $client = Client::model()->findByPk($project->client_id);

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array($project->name, $this->createUrl('project/view', array( 'id' => $project->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Guided Test');
		$this->render('gt/check', array(
            'project' => $project,
            'client' => $client,
            'module' => $module,
            'check' => $check,
            'ratings' => ProjectGtCheck::getRatingNames(),
            'statuses' => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED => Yii::t('app', 'Finished'),
            ),
            'step' => $step + 1,
            'checkCount' => $checkCount
        ));
    }

    /**
     * Display a list of check categories.
     */
	public function actionTarget($id, $target, $page=1) {
        $id = (int) $id;
        $target = (int) $target;
        $page = (int) $page;

        $project = Project::model()->with(array(
            "userHoursAllocated",
            "userHoursSpent",
        ))->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        if (!$project->checkPermission()) {
            throw new CHttpException(403, Yii::t("app", "Access denied."));
        }

        $target = Target::model()->findByAttributes(array(
            "id" => $target,
            "project_id" => $project->id
        ));

        if (!$target) {
            throw new CHttpException(404, Yii::t("app", "Target not found."));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->params["entriesPerPage"];
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];
        $criteria->order = "COALESCE(l10n.name, category.name) ASC";
        $criteria->addCondition("t.target_id = :target_id");
        $criteria->params = array( "target_id" => $target->id );
        $criteria->together = true;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $categories = TargetCheckCategory::model()->with(array(
            "category" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array( "language_id" => $language )
                    ),
                )
            ),
        ))->findAll($criteria);

        $categoryIds = array();

        foreach ($categories as $category) {
            $categoryIds[] = $category->check_category_id;
        }

        $newCriteria = new CDbCriteria();
        $newCriteria->addCondition("t.target_id = :target_id");
        $newCriteria->params = array( "target_id" => $target->id );
        $newCriteria->addInCondition("t.check_category_id", $categoryIds);
        $newCriteria->order = "COALESCE(l10n.name, category.name) ASC";
        $newCriteria->together = true;

        $categories = TargetCheckCategory::model()->with(array(
            "category" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array( "language_id" => $language )
                    ),
                    "controls" => array(
                        "with" => array(
                            "checkCount",
                            "limitedCheckCount",
                        ),
                    ),
                ),
            ),
        ))->findAll($newCriteria);

        $categoryCount = TargetCheckCategory::model()->count($criteria);
        $paginator = new Paginator($categoryCount, $page);

        $criteria = new CDbCriteria();
        $criteria->order = "t.host ASC";
        $criteria->addCondition("t.project_id = :project_id");
        $criteria->params = array("project_id" => $project->id);
        $criteria->together = true;

        $quickTargets = Target::model()->with(array(
            "categories" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    ),
                ),
                "order" => "categories.name",
            )
        ))->findAll($criteria);

        $client = Client::model()->findByPk($project->client_id);

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array( "id" => $project->id )));
        $this->breadcrumbs[] = array($target->hostPort, "");

        // display the page
        $this->pageTitle = $target->hostPort . ($target->description ? " / " . $target->description : "");
		$this->render("target/index", array(
            "project" => $project,
            "target" => $target,
            "client" => $client,
            "categories" => $categories,
            "p" => $paginator,
            "statuses" => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN => Yii::t("app", "Open"),
                Project::STATUS_IN_PROGRESS => Yii::t("app", "In Progress"),
                Project::STATUS_FINISHED => Yii::t("app", "Finished"),
            ),
            "quickTargets" => $quickTargets,
        ));
	}

    /**
     * Project target edit page.
     */
	public function actionEditTarget($id, $target=0) {
        $id = (int) $id;
        $target = (int) $target;
        $newRecord = false;

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        if (!$project->checkPermission()) {
            throw new CHttpException(403, Yii::t("app", "Access denied."));
        }

        if ($target) {
            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }
        } else {
            $target = new Target();
            $newRecord = true;
        }

		$model = new TargetEditForm();
        $model->categoryIds = array();
        $model->referenceIds = array();

        if (!$newRecord) {
            $model->host = $target->host;
            $model->port = $target->port;
            $model->description = $target->description;

            $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                "target_id" => $target->id
            ));

            foreach ($categories as $category) {
                $model->categoryIds[] = $category->check_category_id;
            }

            $references = TargetReference::model()->findAllByAttributes(array(
                "target_id" => $target->id
            ));

            foreach ($references as $reference) {
                $model->referenceIds[] = $reference->reference_id;
            }
        }

		// collect user input data
		if (isset($_POST["TargetEditForm"])) {
            $model->categoryIds = array();
            $model->referenceIds = array();
			$model->attributes = $_POST["TargetEditForm"];

			if ($model->validate()) {
                $target->project_id = $project->id;
                $target->host = $model->host;
                $target->port = $model->port;
                $target->description = $model->description;
                $target->save();

                $addCategories = array();
                $delCategories = array();
                $addReferences = array();
                $delReferences = array();

                if (!$newRecord) {
                    $oldCategories = array();
                    $oldReferences = array();

                    // fill in addCategories & delCategories arrays
                    $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                        "target_id" => $target->id
                    ));

                    foreach ($categories as $category) {
                        $oldCategories[] = $category->check_category_id;
                    }

                    foreach ($oldCategories as $category) {
                        if (!in_array($category, $model->categoryIds)) {
                            $delCategories[] = $category;
                        }
                    }

                    foreach ($model->categoryIds as $category) {
                        if (!in_array($category, $oldCategories)) {
                            $addCategories[] = $category;
                        }
                    }

                    // fill in addReferences & delReferences arrays
                    $references = TargetReference::model()->findAllByAttributes(array(
                        "target_id" => $target->id
                    ));

                    foreach ($references as $reference) {
                        $oldReferences[] = $reference->reference_id;
                    }

                    foreach ($oldReferences as $reference) {
                        if (!in_array($reference, $model->referenceIds)) {
                            $delReferences[] = $reference;
                        }
                    }

                    foreach ($model->referenceIds as $reference) {
                        if (!in_array($reference, $oldReferences)) {
                            $addReferences[] = $reference;
                        }
                    }
                } else {
                    $addCategories = $model->categoryIds;
                    $addReferences = $model->referenceIds;
                }

                // delete categories
                if ($delCategories) {
                    $criteria = new CDbCriteria();

                    $criteria->addInCondition("check_category_id", $delCategories);
                    $criteria->addColumnCondition(array(
                        "target_id" => $target->id
                    ));

                    TargetCheckCategory::model()->deleteAll($criteria);
                }

                // add categories
                foreach ($addCategories as $category) {
                    $targetCategory = new TargetCheckCategory();
                    $targetCategory->target_id = $target->id;
                    $targetCategory->check_category_id = $category;
                    $targetCategory->advanced = true;
                    $targetCategory->save();
                }

                // delete references
                if ($delReferences) {
                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("reference_id", $delReferences);

                    TargetReference::model()->deleteAll($criteria);
                }

                // add references
                foreach ($addReferences as $reference) {
                    $targetReference = new TargetReference();
                    $targetReference->target_id = $target->id;
                    $targetReference->reference_id = $reference;
                    $targetReference->save();
                }

                $target->syncChecks();
                Yii::app()->user->setFlash("success", Yii::t("app", "Target saved."));

                $target->refresh();

                if ($newRecord) {
                    $this->redirect(array("project/edittarget", "id" => $project->id, "target" => $target->id));
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t("app", "New Target"), "");
        } else {
            $this->breadcrumbs[] = array($target->hostPort, $this->createUrl("project/target", array("id" => $project->id, "target" => $target->id)));
            $this->breadcrumbs[] = array(Yii::t("app", "Edit"), "");
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if ($this->_system->demo) {
            $categories = CheckCategory::model()->with(array(
                "l10n" => array(
                    "joinType" => "LEFT JOIN",
                    "on" => "language_id = :language_id",
                    "params" => array( "language_id" => $language )
                ),
                "controls" => array(
                    "with" => array(
                        "checkCount",
                        "limitedCheckCount"
                    )
                )
            ))->findAllByAttributes(
                array(),
                array("order" => "COALESCE(l10n.name, t.name) ASC")
            );
        } else {
            $categories = CheckCategory::model()->with(array(
                "l10n" => array(
                    "joinType" => "LEFT JOIN",
                    "on" => "language_id = :language_id",
                    "params" => array( "language_id" => $language )
                ),
            ))->findAllByAttributes(
                array(),
                array("order" => "COALESCE(l10n.name, t.name) ASC")
            );
        }

        $references = Reference::model()->findAllByAttributes(
            array(),
            array("order" => "t.name ASC")
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t("app", "New Target") : $target->hostPort . ($target->description ? " / " . $target->description : "");
		$this->render("target/edit", array(
            "model" => $model,
            "project" => $project,
            "target" => $target,
            "categories" => $categories,
            "references" => $references
        ));
	}

    /**
     * Display a list of checks.
     */
	public function actionChecks($id, $target, $category) {
        $id = (int) $id;
        $target = (int) $target;
        $category = (int) $category;

        $project = Project::model()->with(array(
            "userHoursAllocated",
            "userHoursSpent",
        ))->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        if (!$project->checkPermission()) {
            throw new CHttpException(403, Yii::t("app", "Access denied."));
        }

        $target = Target::model()->findByAttributes(array(
            "id" => $target,
            "project_id" => $project->id
        ));

        if (!$target) {
            throw new CHttpException(404, Yii::t("app", "Target not found."));
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = TargetCheckCategory::model()->with(array(
            "category" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                )
            )
        ))->findByAttributes(array(
            "target_id" => $target->id,
            "check_category_id" => $category
        ));

        if (!$category) {
            throw new CHttpException(404, Yii::t("app", "Category not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("t.check_category_id" => $category->check_category_id));
        $criteria->order = "t.sort_order ASC";

        $controls = CheckControl::model()->with(array(
            "customChecks" => array(
                "alias" => "custom",
                "on" => "custom.target_id = :target_id",
                "params" => array("target_id" => $target->id)
            ),
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "l10n.language_id = :language_id",
                "params" => array("language_id" => $language)
            ),
            "checks" => array(
                "with" => array(
                    "targetChecks" => array(
                        "joinType" => "LEFT JOIN",
                        "alias" => "tc",
                        "on" => "tc.target_id = :target_id",
                        "params" => array("target_id" => $target->id),
                    )
                )
            )
        ))->findAll($criteria);

        $stats = array();

        foreach ($controls as $control) {
            if (!isset($stats[$control->id])) {
                $stats[$control->id] = array(
                    "checks" => 0,
                    "finished" => 0,
                    "info" => 0,
                    "lowRisk" => 0,
                    "medRisk" => 0,
                    "highRisk" => 0,
                );

                if ($control->customChecks) {
                    foreach ($control->customChecks as $customCheck) {
                        $stats[$control->id]["checks"]++;
                        $stats[$control->id]["finished"]++;

                        switch ($customCheck->rating) {
                            case TargetCustomCheck::RATING_INFO:
                                $stats[$control->id]["info"]++;
                                break;

                            case TargetCustomCheck::RATING_LOW_RISK:
                                $stats[$control->id]["lowRisk"]++;
                                break;

                            case TargetCustomCheck::RATING_MED_RISK:
                                $stats[$control->id]["medRisk"]++;
                                break;

                            case TargetCustomCheck::RATING_HIGH_RISK:
                                $stats[$control->id]["highRisk"]++;
                                break;

                            default:
                                break;
                        }
                    }
                }
            }

            $stats[$control->id]["checks"]++;

            foreach ($control->checks as $check) {
                foreach ($check->targetChecks as $tc) {
                    $stats[$control->id]["checks"]++;

                    if ($tc->status == TargetCheck::STATUS_FINISHED) {
                        $stats[$control->id]["finished"]++;
                    }

                    switch ($tc->rating) {
                        case TargetCheck::RATING_INFO:
                            $stats[$control->id]["info"]++;
                            break;

                        case TargetCheck::RATING_LOW_RISK:
                            $stats[$control->id]["lowRisk"]++;
                            break;

                        case TargetCheck::RATING_MED_RISK:
                            $stats[$control->id]["medRisk"]++;
                            break;

                        case TargetCheck::RATING_HIGH_RISK:
                            $stats[$control->id]["highRisk"]++;
                            break;
                    }
                }
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = "t.host ASC";
        $criteria->addCondition("t.project_id = :project_id");
        $criteria->params = array("project_id" => $project->id);
        $criteria->together = true;

        $quickTargets = Target::model()->with(array(
            "categories" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    ),
                ),
                "order" => "categories.name",
            )
        ))->findAll($criteria);

        $client = Client::model()->findByPk($project->client_id);
        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
        $this->breadcrumbs[] = array($target->hostPort, $this->createUrl("project/target", array(
            "id" => $project->id,
            "target" => $target->id
        )));
        $this->breadcrumbs[] = array($category->category->localizedName, "");

        // display the page
        $this->pageTitle = $category->category->localizedName;
		$this->render("target/check/index", array(
            "project" => $project,
            "target" => $target,
            "client" => $client,
            "category" => $category,
            "controls" => $controls,
            "statuses" => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN => Yii::t("app", "Open"),
                Project::STATUS_IN_PROGRESS => Yii::t("app", "In Progress"),
                Project::STATUS_FINISHED => Yii::t("app", "Finished"),
            ),
            "ratings" => TargetCheck::getRatingNames(),
            "stats" => $stats,
            "quickTargets" => $quickTargets,
        ));
	}

    /**
     * List of checks in control
     * @param $id
     * @param $target
     * @param $category
     * @param $control
     * @throws CHttpException
     */
    public function actionControlChecklist($id, $target, $category, $control) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $language = Language::model()->findByAttributes(array(
                "code" => Yii::app()->language
            ));

            if ($language) {
                $language = $language->id;
            }

            $category = TargetCheckCategory::model()->findByAttributes(array(
                "target_id" => $target->id,
                "check_category_id" => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t("app", "Category not found."));
            }

            $control = CheckControl::model()->findByAttributes(array(
                "id" => $control,
                "check_category_id" => $category->check_category_id,
            ));

            if (!$control) {
                throw new CHttpException(404, Yii::t("app", "Control not found."));
            }

            $referenceIds = array();
            $references = TargetReference::model()->findAllByAttributes(array(
                "target_id" => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array("t.check_control_id" => $control->id));
            $criteria->addInCondition("t.reference_id", $referenceIds);
            $criteria->order = "t.sort_order ASC, tc.id ASC";

            if (!$category->advanced) {
                $criteria->addCondition("t.advanced = FALSE");
            }

            $checks = Check::model()->with(array(
                "l10n" => array(
                    "joinType" => "LEFT JOIN",
                    "on" => "l10n.language_id = :language_id",
                    "params" => array("language_id" => $language)
                ),
                "targetChecks" => array(
                    "alias" => "tc",
                    "joinType" => "LEFT JOIN",
                    "on" => "tc.target_id = :target_id",
                    "params" => array("target_id" => $target->id),
                ),
            ))->findAll($criteria);

            $html = "";

            foreach ($checks as $check) {
                $number = 0;

                foreach ($check->targetChecks as $tc) {
                    $html .= $this->renderPartial("partial/check-header", array(
                        "project" => $project,
                        "target" => $target,
                        "category" => $category,
                        "check" => $check,
                        "tc" => $tc,
                        "limited" => ($this->_system->demo && !$check->demo),
                        "number" => $number,
                        "ratings" => TargetCheck::getRatingNames(),
                    ), true);

                    $number++;
                }
            }

            $response->addData("html", $html);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Check form
     * @param $id
     * @param $target
     * @param $category
     * @param $check
     * @throws CHttpException
     */
    public function actionCheck($id, $target, $category, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $category = TargetCheckCategory::model()->with("category")->findByAttributes(array(
                "target_id" => $target->id,
                "check_category_id" => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t("app", "Category not found."));
            }

            $language = Language::model()->findByAttributes(array(
                "code" => Yii::app()->language
            ));

            if (!$language) {
                $language = Language::model()->findByAttributes(array(
                    "default" => true
                ));
            }

            /** @var TargetCheck $check */
            $check = TargetCheck::model()->with(array(
                "check" => array(
                    "with" => array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => array("language_id" => $language->id)
                        ),
                        "scripts" => array(
                            "joinType" => "LEFT JOIN",
                            "with" => array(
                                "inputs" => array(
                                    "on" => "inputs.visible AND inputs.check_script_id = scripts.id",
                                    "joinType" => "LEFT JOIN",
                                    "with" => array(
                                        "l10n" => array(
                                            "alias" => "l10n_i",
                                            "joinType" => "LEFT JOIN",
                                            "on" => "l10n_i.language_id = :language_id",
                                            "params" => array("language_id" => $language->id)
                                        )
                                    ),
                                    "order" => "inputs.sort_order ASC"
                                ),
                            )
                        ),
                        "results" => array(
                            "joinType" => "LEFT JOIN",
                            "with" => array(
                                "l10n" => array(
                                    "alias" => "l10n_r",
                                    "joinType" => "LEFT JOIN",
                                    "on" => "l10n_r.language_id = :language_id",
                                    "params" => array("language_id" => $language->id)
                                )
                            ),
                            "order" => "results.sort_order ASC"
                        ),
                        "solutions" => array(
                            "joinType" => "LEFT JOIN",
                            "with" => array(
                                "l10n" => array(
                                    "alias" => "l10n_s",
                                    "joinType" => "LEFT JOIN",
                                    "on" => "l10n_s.language_id = :language_id",
                                    "params" => array("language_id" => $language->id)
                                )
                            ),
                            "order" => "solutions.sort_order ASC"
                        ),
                        "_reference"
                    ),
                ),
                "attachments",
                "inputs" => array(
                    "alias" => "tci",
                ),
                "solutions" => array(
                    "alias" => "tcs",
                ),
            ))->findByAttributes(array(
                "id" => $check,
                "target_id" => $target->id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            // display the check form
            $html = $this->renderPartial("partial/check-form", array(
                "project" => $project,
                "target" => $target,
                "category" => $category,
                "check" => $check,
                "limited" => ($this->_system->demo && !$check->check->demo),
                "ratings" => TargetCheck::getRatingNames(),
            ), true);

            $response->addData("html", $html);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Save check.
     */
    public function actionSaveCheck($id, $target, $category, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $category = TargetCheckCategory::model()->with("category")->findByAttributes(array(
                "target_id" => $target->id,
                "check_category_id" => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t("app", "Category not found."));
            }

            $controls = CheckControl::model()->findAllByAttributes(array(
                "check_category_id" => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            /** @var TargetCheck $targetCheck */
            $targetCheck = TargetCheck::model()->findByAttributes(array(
                "id" => $check,
                "target_id" => $target->id,
            ));

            if (!$targetCheck) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition("check_control_id", $controlIds);
            $criteria->addColumnCondition(array(
                "id" => $targetCheck->check_id
            ));

            $check = Check::model()->find($criteria);

            if (!$check) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $model = new TargetCheckEditForm();
            $model->attributes = $_POST["TargetCheckEditForm_" . $targetCheck->id];

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

            if (!$language) {
                $language = Language::model()->findByAttributes(array(
                    "default" => true
                ));
            }

            if (!$model->overrideTarget) {
                $model->overrideTarget = null;
            }

            if (!$model->protocol) {
                $model->protocol = null;
            }

            if (!$model->port) {
                $model->port = null;
            }

            if ($model->result == "") {
                $model->result = null;
            }

            if ($model->poc == "") {
                $model->poc = null;
            }

            if ($model->links == "") {
                $model->links = null;
            }

            $targetCheck->user_id = Yii::app()->user->id;
            $targetCheck->language_id = $language->id;
            $targetCheck->override_target = $model->overrideTarget;
            $targetCheck->protocol = $model->protocol;
            $targetCheck->port = $model->port;
            $targetCheck->status = TargetCheck::STATUS_FINISHED;
            $targetCheck->rating = $model->rating;
            $targetCheck->poc = $model->poc;
            $targetCheck->links = $model->links;

            if (User::checkRole(User::ROLE_ADMIN) && $model->saveResult) {
                if (!$model->resultTitle) {
                    throw new CHttpException(403, Yii::t("app", "Please specify the result title."));
                }

                $criteria = new CDbCriteria();
                $criteria->select = "MAX(sort_order) as max_sort_order";
                $criteria->addColumnCondition(array("check_id" => $check->id));

                $maxOrder = CheckResult::model()->find($criteria);
                $sortOrder = 0;

                if ($maxOrder && $maxOrder->max_sort_order !== null) {
                    $sortOrder = $maxOrder->max_sort_order + 1;
                }

                $result = new CheckResult();
                $result->check_id = $check->id;
                $result->title = $model->resultTitle;
                $result->result = $model->result;
                $result->sort_order = $sortOrder;
                $result->save();

                $resultL10n = new CheckResultL10n();
                $resultL10n->check_result_id = $result->id;
                $resultL10n->language_id = $language->id;
                $resultL10n->title = $model->resultTitle;
                $resultL10n->result = $model->result;
                $resultL10n->save();

                $response->addData("newResult", array(
                    "id" => $result->id,
                    "title" => $result->title,
                    "result" => $result->result,
                    "targetCheck" => array(
                        "id" => $targetCheck->id,
                        "check" => array(
                            "id" =>$check->id
                        )
                    )
                ));
            } else {
                $targetCheck->result = $model->result;
            }

            $targetCheck->save();

            // delete old solutions
            TargetCheckSolution::model()->deleteAllByAttributes(array(
                "target_check_id" => $targetCheck->id,
            ));

            // delete old inputs
            TargetCheckInput::model()->deleteAllByAttributes(array(
                "target_check_id" => $targetCheck->id,
            ));

            // delete old vulnerabilities
            TargetCheckVuln::model()->deleteAllByAttributes(array(
                "target_check_id" => $targetCheck->id,
            ));

            // add solutions
            if ($model->solutions) {
                $hasCustom = false;

                foreach ($model->solutions as $solutionId) {
                    // reset solution
                    if (!$solutionId) {
                        break;
                    }

                    // custom solution
                    if ($solutionId == TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER) {
                        $hasCustom = true;
                        continue;
                    }

                    $solution = CheckSolution::model()->findByAttributes(array(
                        "id" => $solutionId,
                        "check_id" => $check->id
                    ));

                    if (!$solution) {
                        throw new CHttpException(404, Yii::t("app", "Solution not found."));
                    }

                    $solution = new TargetCheckSolution();
                    $solution->target_check_id = $targetCheck->id;
                    $solution->check_solution_id = $solutionId;
                    $solution->save();
                }

                if ($hasCustom && $model->solution) {
                    if (User::checkRole(User::ROLE_ADMIN) && $model->saveSolution) {
                        if (!$model->solutionTitle) {
                            throw new CHttpException(403, Yii::t("app", "Please specify the solution title."));
                        }
                        
                        $criteria = new CDbCriteria();
                        $criteria->select = "MAX(sort_order) as max_sort_order";
                        $criteria->addColumnCondition(array("check_id" => $check->id));
            
                        $maxOrder = CheckSolution::model()->find($criteria);
                        $sortOrder = 0;
            
                        if ($maxOrder && $maxOrder->max_sort_order !== null) {
                            $sortOrder = $maxOrder->max_sort_order + 1;
                        }
                                    
                        $solution = new CheckSolution();
                        $solution->check_id = $check->id;
                        $solution->title = $model->solutionTitle;
                        $solution->solution = $model->solution;
                        $solution->sort_order = $sortOrder;
                        $solution->save();

                        $solutionL10n = new CheckSolutionL10n();
                        $solutionL10n->check_solution_id = $solution->id;
                        $solutionL10n->language_id = $language->id;
                        $solutionL10n->title = $model->solutionTitle;
                        $solutionL10n->solution = $model->solution;
                        $solutionL10n->save();

                        $checkSolution = new TargetCheckSolution();
                        $checkSolution->target_check_id = $targetCheck->id;
                        $checkSolution->check_solution_id = $solution->id;
                        $checkSolution->save();

                        $targetCheck->solution = null;
                        $targetCheck->solution_title = null;
                        $targetCheck->save();
                    } else {
                        $targetCheck->solution = $model->solution;
                        $targetCheck->solution_title = $model->solutionTitle;
                        $targetCheck->save();
                    }
                }
            }

            // add inputs
            if ($check->automated) {
                // initialize all hidden inputs, if any
                foreach ($check->scripts as $script) {
                    foreach ($script->inputs as $hiddenInput) {
                        if (!$hiddenInput->visible) {
                            $input = new TargetCheckInput();
                            $input->target_check_id = $targetCheck->id;
                            $input->check_input_id = $hiddenInput->id;
                            $input->value = $hiddenInput->value;
                            $input->save();
                        }
                    }
                }

                // visible inputs
                if ($model->inputs) {
                    foreach ($model->inputs as $inputId => $inputValue) {
                        $input = CheckInput::model()->findByAttributes(array(
                            "id" => $inputId,
                        ));

                        if (!$input || !$input->visible) {
                            throw new CHttpException(404, Yii::t("app", "Input not found."));
                        }

                        if ($inputValue == "") {
                            $inputValue = null;
                        }

                        $input = new TargetCheckInput();
                        $input->target_check_id = $targetCheck->id;
                        $input->check_input_id = $inputId;
                        $input->value = $inputValue;
                        $input->save();
                    }
                }
            }

            $response->addData("targetCheck", array(
                'id' => $targetCheck->id,
                "check" => array(
                    "id" => $check->id
                )
            ));

            $response->addData("rating", $targetCheck->rating);

            if ($project->status == Project::STATUS_OPEN) {
                $project->status = Project::STATUS_IN_PROGRESS;
                $project->save();
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Check autosave
     */
    public function actionAutosaveCheck($id, $target, $category, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $category = TargetCheckCategory::model()->with("category")->findByAttributes(array(
                "target_id" => $target->id,
                "check_category_id" => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t("app", "Category not found."));
            }

            $controls = CheckControl::model()->findAllByAttributes(array(
                "check_category_id" => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            /** @var TargetCheck $targetCheck */
            $targetCheck = TargetCheck::model()->findByAttributes(array(
                "id" => $check,
                "target_id" => $target->id,
            ));

            if (!$targetCheck) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition("check_control_id", $controlIds);
            $criteria->addColumnCondition(array(
                "id" => $targetCheck->check_id
            ));

            $check = Check::model()->find($criteria);

            if (!$check) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $model = new TargetCheckEditForm();
            $model->attributes = $_POST["TargetCheckEditForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $targetCheck->result = $model->result;
            $targetCheck->save();

            if ($project->status == Project::STATUS_OPEN) {
                $project->status = Project::STATUS_IN_PROGRESS;
                $project->save();
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Save custom check.
     */
    public function actionSaveCustomCheck($id, $target, $category) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $form = new TargetCustomCheckEditForm();
            $form->attributes = $_POST["TargetCustomCheckEditForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            if ($form->id) {
                $customCheck = TargetCustomCheck::model()->with("control")->findByAttributes(array(
                    "id" => $form->id,
                    "target_id" => $target->id,
                ));

                if (!$customCheck || $customCheck->control->check_category_id != $category) {
                    throw new CHttpException(404, Yii::t("app", "Custom check not found."));
                }

                $control = $customCheck->control;
            } else {
                $control = CheckControl::model()->findByPk($form->controlId);

                if (!$control || $control->check_category_id != $category) {
                    throw new CHttpException(404, Yii::t("app", "Control not found."));
                }

                $categoryCheck = TargetCheckCategory::model()->findByAttributes(array(
                    "target_id" => $target->id,
                    "check_category_id" => $control->check_category_id
                ));

                if (!$categoryCheck) {
                    throw new CHttpException(404, Yii::t("app", "Control not found."));
                }

                $customCheck = new TargetCustomCheck();
                $customCheck->target_id = $target->id;
                $customCheck->check_control_id = $control->id;

                $criteria = new CDbCriteria();
                $criteria->select = "MAX(reference) as max_reference";

                $maxReference = TargetCustomCheck::model()->find($criteria);
                $reference = 1;

                if ($maxReference && $maxReference->max_reference !== null) {
                    $reference = $maxReference->max_reference + 1;
                }

                $customCheck->reference = $reference;
            }

            if (!$form->name) {
                $form->name = null;
            }

            if (!$form->backgroundInfo) {
                $form->backgroundInfo = null;
            }

            if (!$form->question) {
                $form->question = null;
            }

            if ($form->result == "") {
                $form->result = null;
            }

            if (!$form->poc) {
                $form->poc = null;
            }

            if (!$form->links) {
                $form->links = null;
            }

            if (!$form->solution) {
                $form->solution = null;
            }

            if (!$form->solutionTitle) {
                $form->solutionTitle = null;
            }

            if ($form->createCheck) {
                $reference = Reference::model()->findByAttributes(array("name" => "CUSTOM"));

                if (!$reference) {
                    $reference = Reference::model()->find();
                }

                if (!$reference) {
                   throw new CHttpException(500, Yii::t("app", "At least one reference should be created first."));
                }

                $language = Language::model()->findByAttributes(array(
                    "code" => Yii::app()->language
                ));

                if (!$language) {
                    $language = Language::model()->findByAttributes(array(
                        "default" => true
                    ));
                }

                if ($this->_system->demo) {
                    $updated = System::model()->updateCounters(
                        array("demo_check_limit" => -1),
                        array("condition" => "id = 1 AND demo_check_limit > 0")
                    );

                    if (!$updated) {
                        throw new CHttpException(403, Yii::t("app", "You've exceeded a limit of the new checks for the demo version."));
                    }
                }

                $check = new Check();
                $check->demo = true;
                $check->name = $form->name;
                $check->background_info = $form->backgroundInfo;
                $check->question = $form->question;
                $check->check_control_id = $control->id;
                $check->reference_id = $reference->id;
                $check->reference_code = "CHECK-" . $customCheck->reference;
                $check->reference_url = $reference->url;
                $check->advanced = false;
                $check->automated = false;
                $check->multiple_solutions = false;
                $check->status = Check::STATUS_INSTALLED;
                $check->save();

                $check->sort_order = $check->id;
                $check->save();

                $checkL10n = new CheckL10n();
                $checkL10n->check_id = $check->id;
                $checkL10n->language_id = $language->id;
                $checkL10n->background_info = $form->backgroundInfo;
                $checkL10n->question = $form->question;
                $checkL10n->name = $form->name;
                $checkL10n->save();

                $targetCheck = new TargetCheck();
                $targetCheck->target_id = $target->id;
                $targetCheck->check_id = $check->id;
                $targetCheck->user_id = Yii::app()->user->id;
                $targetCheck->language_id = $language->id;
                $targetCheck->result = $form->result;
                $targetCheck->status = TargetCheck::STATUS_FINISHED;
                $targetCheck->rating = $form->rating;
                $targetCheck->poc = $form->poc;
                $targetCheck->links = $form->links;
                $targetCheck->save();

                if ($form->solutionTitle && $form->solution) {
                    $solution = new CheckSolution();
                    $solution->check_id = $check->id;
                    $solution->sort_order = 0;
                    $solution->title = $form->solutionTitle;
                    $solution->solution = $form->solution;
                    $solution->save();

                    $solutionL10n = new CheckSolutionL10n();
                    $solutionL10n->check_solution_id = $solution->id;
                    $solutionL10n->language_id = $language->id;
                    $solutionL10n->title = $form->solutionTitle;
                    $solutionL10n->solution = $form->solution;
                    $solutionL10n->save();

                    $checkSolution = new TargetCheckSolution();
                    $checkSolution->target_check_id = $targetCheck->id;
                    $checkSolution->check_solution_id = $solution->id;
                    $checkSolution->save();
                }

                if (!$customCheck->isNewRecord) {
                    $customCheck->delete();
                }

                $response->addData("createCheck", true);
            } else {
                $customCheck->user_id = Yii::app()->user->id;
                $customCheck->name = $form->name;
                $customCheck->background_info = $form->backgroundInfo;
                $customCheck->question = $form->question;
                $customCheck->result = $form->result;
                $customCheck->solution_title = $form->solutionTitle;
                $customCheck->solution = $form->solution;
                $customCheck->rating = $form->rating;
                $customCheck->poc = $form->poc;
                $customCheck->links = $form->links;
                $customCheck->save();

                $response->addData("rating", $customCheck->rating);
            }

            if ($project->status == Project::STATUS_OPEN) {
                $project->status = Project::STATUS_IN_PROGRESS;
                $project->save();
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Save check category.
     */
    public function actionSaveCategory($id, $target, $category)
    {
        $response = new AjaxResponse();

        try
        {
            $id       = (int) $id;
            $target   = (int) $target;
            $category = (int) $category;

            $project = Project::model()->findByPk($id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            if (!$project->checkPermission())
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));

            $target = Target::model()->findByAttributes(array(
                'id'         => $target,
                'project_id' => $project->id
            ));

            if (!$target)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id'         => $target->id,
                'check_category_id' => $category
            ));

            if (!$category)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            $model = new TargetCheckCategoryEditForm();
            $model->attributes = $_POST['TargetCheckCategoryEditForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $category->advanced = $model->advanced;
            $category->save();
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control function.
     */
    public function actionControl()
    {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id = $model->id;
            $project = Project::model()->findByPk($id);

            if ($project === null) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            switch ($model->operation) {
                case 'delete':
                    if (!User::checkRole(User::ROLE_ADMIN)) {
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));
                    }

                    $project->delete();
                    break;

                case 'gt':
                    if (count($project->targets) > 0 || count($project->modules) > 0) {
                        throw new CHttpException(403, Yii::t('app', 'Project is not empty.'));
                    }

                    if ($project->guided_test) {
                        $project->guided_test = false;
                    } else {
                        $project->guided_test = true;
                    }

                    $project->save();

                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control target function.
     */
    public function actionControlTarget()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id     = $model->id;
            $target = Target::model()->with('project')->findByPk($id);

            if ($target === null)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            if (!$target->project->checkPermission())
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));

            switch ($model->operation)
            {
                case 'delete':
                    $target->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control detail function.
     */
    public function actionControlDetail()
    {
        $response = new AjaxResponse();

        try
        {
            if (!User::checkRole(User::ROLE_ADMIN) && !User::checkRole(User::ROLE_CLIENT))
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));

            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id     = $model->id;
            $detail = ProjectDetail::model()->with('project')->findByPk($id);

            if ($detail === null)
                throw new CHttpException(404, Yii::t('app', 'Detail not found.'));

            if (!$detail->project->checkPermission())
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));

            switch ($model->operation)
            {
                case 'delete':
                    $detail->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Upload attachment function.
     */
    function actionUploadAttachment($id, $target, $category, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $target = Target::model()->findByAttributes(array(
                'id' => $target,
                'project_id' => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));
            }

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id' => $target->id,
                'check_category_id' => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));
            }

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            $targetCheck = TargetCheck::model()->findByAttributes(array(
                "id" => $check,
                "target_id" => $target->id,
            ));

            if (!$targetCheck) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition('check_control_id', $controlIds);
            $criteria->addColumnCondition(array(
                'id' => $targetCheck->check_id
            ));

            $check = Check::model()->find($criteria);

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            $model = new TargetCheckAttachmentUploadForm();
            $model->attachment = CUploadedFile::getInstanceByName('TargetCheckAttachmentUploadForm[attachment]');

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $attachment = new TargetCheckAttachment();
            $attachment->target_check_id = $targetCheck->id;
            $attachment->name = $model->attachment->name;
            $attachment->type = $model->attachment->type;
            $attachment->size = $model->attachment->size;
            $attachment->path = hash('sha256', $attachment->name . rand() . time());
            $attachment->save();

            $model->attachment->saveAs(Yii::app()->params['attachments']['path'] . '/' . $attachment->path);

            $response->addData('name', CHtml::encode($attachment->name));
            $response->addData('url', $this->createUrl('project/attachment', array( 'path' => $attachment->path )));
            $response->addData('path', $attachment->path);
            $response->addData('controlUrl', $this->createUrl('project/controlattachment'));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Upload attachment function for GT.
     */
    function actionGtUploadAttachment($id, $module, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $module = (int) $module;
            $check = (int) $check;

            $module = (int) $module;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->guided_test || !$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $module = ProjectGtModule::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_module_id' => $module
            ));

            if (!$module) {
                throw new CHttpException(404, Yii::t('app', 'Module not found.'));
            }

            $check = GtCheck::model()->with('check')->findByAttributes(array(
                'id' => $check,
                'gt_module_id' => $module->gt_module_id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_check_id' => $check->id
            ));

            if (!$projectCheck) {
                $language = Language::model()->findByAttributes(array(
                    "code" => Yii::app()->language
                ));

                if (!$language) {
                    $language = Language::model()->findByAttributes(array(
                        "default" => true
                    ));
                }

                $projectCheck = new ProjectGtCheck();
                $projectCheck->project_id = $project->id;
                $projectCheck->gt_check_id = $check->id;
                $projectCheck->user_id = Yii::app()->user->id;
                $projectCheck->language_id = $language->id;
                $projectCheck->save();
            }

            $model = new ProjectGtCheckAttachmentUploadForm();
            $model->attachment = CUploadedFile::getInstanceByName('ProjectGtCheckAttachmentUploadForm[attachment]');

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $attachment = new ProjectGtCheckAttachment();
            $attachment->project_id = $project->id;
            $attachment->gt_check_id = $check->id;
            $attachment->name = $model->attachment->name;
            $attachment->type = $model->attachment->type;
            $attachment->size = $model->attachment->size;
            $attachment->path = hash('sha256', $attachment->name . rand() . time());
            $attachment->save();

            $model->attachment->saveAs(Yii::app()->params['attachments']['path'] . '/' . $attachment->path);

            $response->addData('name', CHtml::encode($attachment->name));
            $response->addData('url', $this->createUrl('project/gtattachment', array('path' => $attachment->path)));
            $response->addData('path', $attachment->path);
            $response->addData('controlUrl', $this->createUrl('project/gtcontrolattachment'));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control attachment.
     */
    public function actionControlAttachment() {
        $response = new AjaxResponse();

        try {
            $model = new TargetCheckAttachmentControlForm();
            $model->attributes = $_POST["TargetCheckAttachmentControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $path = $model->path;
            $attachment = TargetCheckAttachment::model()->with(array(
                "targetCheck" => array(
                    "with" => array(
                        "target" => array(
                            "with" => "project"
                        )
                    )
                )
            ))->findByAttributes(array(
                "path" => $path
            ));

            if ($attachment === null) {
                throw new CHttpException(404, Yii::t("app", "Attachment not found."));
            }

            if (!$attachment->targetCheck->target->project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            switch ($model->operation) {
                case "delete":
                    $attachment->delete();
                    @unlink(Yii::app()->params["attachments"]["path"] . "/" . $attachment->path);
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

    /**
     * Control GT attachment.
     */
    public function actionGtControlAttachment()
    {
        $response = new AjaxResponse();

        try {
            $model = new ProjectGtCheckAttachmentControlForm();
            $model->attributes = $_POST['ProjectGtCheckAttachmentControlForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $path = $model->path;
            $attachment = ProjectGtCheckAttachment::model()->with('project')->findByAttributes(array(
                'path' => $path
            ));

            if ($attachment === null) {
                throw new CHttpException(404, Yii::t('app', 'Attachment not found.'));
            }

            if (!$attachment->project->guided_test || !$attachment->project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            switch ($model->operation) {
                case 'delete':
                    $attachment->delete();
                    @unlink(Yii::app()->params['attachments']['path'] . '/' . $attachment->path);
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control GT target.
     */
    public function actionGtControlTarget()
    {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $target = ProjectGtSuggestedTarget::model()->with('project')->findByPk($model->id);

            if ($target === null) {
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));
            }

            if (!$target->project->guided_test || !$target->project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            switch ($model->operation) {
                case 'approve':
                    $target->approved = true;
                    $target->save();

                    $module = ProjectGtModule::model()->findByAttributes(array(
                        'project_id' => $target->project_id,
                        'gt_module_id' => $target->gt_module_id
                    ));

                    if (!$module) {
                        $criteria = new CDbCriteria();
                        $criteria->select = 'MAX(sort_order) as max_sort_order';
                        $criteria->addColumnCondition(array('project_id' => $target->project_id));

                        $maxOrder = ProjectGtModule::model()->find($criteria);
                        $sortOrder = 0;

                        if ($maxOrder && $maxOrder->max_sort_order !== null) {
                            $sortOrder = $maxOrder->max_sort_order + 1;
                        }

                        $module = new ProjectGtModule();
                        $module->project_id = $target->project_id;
                        $module->gt_module_id = $target->gt_module_id;
                        $module->sort_order = $sortOrder;
                        $module->save();
                    }

                    break;

                case 'delete':
                    $target->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Get attachment.
     */
    public function actionAttachment($path) {
        $attachment = TargetCheckAttachment::model()->with(array(
            "targetCheck" => array(
                "with" => array(
                    "target" => array(
                        "with" => "project"
                    )
                )
            )
        ))->findByAttributes(array(
            "path" => $path
        ));

        if ($attachment === null) {
            throw new CHttpException(404, Yii::t("app", "Attachment not found."));
        }

        if (!$attachment->targetCheck->target->project->checkPermission()) {
            throw new CHttpException(403, Yii::t("app", "Access denied."));
        }

        $filePath = Yii::app()->params["attachments"]["path"] . "/" . $attachment->path;

        if (!file_exists($filePath)) {
            throw new CHttpException(404, Yii::t("app", "Attachment not found."));
        }

        // give user a file
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . str_replace("\"", "", $attachment->name) . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . $attachment->size);

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Get GT attachment.
     */
    public function actionGtAttachment($path)
    {
        $attachment = ProjectGtCheckAttachment::model()->with('project')->findByAttributes(array(
            'path' => $path
        ));

        if ($attachment === null) {
            throw new CHttpException(404, Yii::t('app', 'Attachment not found.'));
        }

        if (!$attachment->project->guided_test || !$attachment->project->checkPermission()) {
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        $filePath = Yii::app()->params['attachments']['path'] . '/' . $attachment->path;

        if (!file_exists($filePath)) {
            throw new CHttpException(404, Yii::t('app', 'Attachment not found.'));
        }

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $attachment->name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $attachment->size);

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Control check function.
     */
    public function actionControlCheck($id, $target, $category, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $target = Target::model()->findByAttributes(array(
                'id' => $target,
                'project_id' => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));
            }

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id' => $target->id,
                'check_category_id' => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));
            }

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            /** @var TargetCheck $targetCheck */
            $targetCheck = TargetCheck::model()->findByAttributes(array(
                "id" => $check,
                "target_id" => $target->id,
            ));
            
            if (!$targetCheck) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition('check_control_id', $controlIds);
            $criteria->addColumnCondition(array(
                'id' => $targetCheck->check_id
            ));

            $check = Check::model()->find($criteria);

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            if ($this->_system->demo && !$check->demo) {
                throw new CHttpException(403, Yii::t("app", "This check is not available in the demo version."));
            }

            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if (!$language) {
                $language = Language::model()->findByAttributes(array(
                    "default" => true
                ));
            }

            switch ($model->operation)             {
                case "start":
                    if (!in_array($targetCheck->status, array(TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED))) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    // delete solutions
                    TargetCheckSolution::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    try {
                        SystemManager::updateStatus(
                            System::STATUS_RUNNING,
                            array(System::STATUS_IDLE, System::STATUS_RUNNING)
                        );
                    } catch (Exception $e) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    $targetCheck->status = TargetCheck::STATUS_IN_PROGRESS;
                    $targetCheck->rating = TargetCheck::RATING_NONE;
                    $targetCheck->started = null;
                    $targetCheck->pid = null;
                    $targetCheck->save();

                    break;

                case "stop":
                    if ($targetCheck->status != TargetCheck::STATUS_IN_PROGRESS) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    $targetCheck->status = TargetCheck::STATUS_STOP;
                    $targetCheck->save();

                    break;

                case "reset":
                    if (!in_array($targetCheck->status, array(TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED))) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    // delete solutions
                    TargetCheckSolution::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    // delete inputs
                    TargetCheckInput::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    // delete vulnerabilities
                    TargetCheckVuln::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    // delete files
                    TargetCheckAttachment::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    $targetCheck->result = null;
                    $targetCheck->target_file = null;
                    $targetCheck->pid = null;
                    $targetCheck->started = null;
                    $targetCheck->status = TargetCheck::STATUS_OPEN;
                    $targetCheck->result_file = null;
                    $targetCheck->protocol = $check->protocol;
                    $targetCheck->port = $check->port;
                    $targetCheck->override_target = null;
                    $targetCheck->table_result = null;
                    $targetCheck->solution = null;
                    $targetCheck->solution_title = null;
                    $targetCheck->save();

                    $response->addData("automated", $check->automated);
                    $response->addData("protocol",  $check->protocol);
                    $response->addData("port", $check->port);
                    $inputValues = array();

                    // get default input values
                    if ($check->automated) {
                        $scripts = CheckScript::model()->findAllByAttributes(array(
                            "check_id" => $check->id
                        ));

                        $scriptIds = array();

                        foreach ($scripts as $script) {
                            $scriptIds[] = $script->id;
                        }

                        $criteria = new CDbCriteria();
                        $criteria->addInCondition("check_script_id", $scriptIds);
                        $criteria->addCondition("visible");

                        $inputs = CheckInput::model()->findAll($criteria);

                        foreach ($inputs as $input) {
                            $inputValues[] = array(
                                "id" => "TargetCheckEditForm_" . $targetCheck->id . "_inputs_" . $input->id,
                                "value" => $input->value
                            );
                        }
                    }

                    $response->addData("inputs", $inputValues);

                    break;

                case "copy":
                    $copy = new TargetCheck();
                    $copy->target_id = $targetCheck->target_id;
                    $copy->check_id = $targetCheck->check_id;
                    $copy->status = TargetCheck::STATUS_OPEN;
                    $copy->user_id = Yii::app()->user->id;
                    $copy->rating = TargetCheck::RATING_NONE;
                    $copy->language_id = $language->id;
                    $copy->save();

                    $response->addData("id", $copy->id);

                    break;

                case "delete":
                    $count = count($targetCheck->check->targetChecks);

                    if ($count <= 1) {
                        throw new CHttpException(403, Yii::t("app", "Only duplicate checks can be removed."));
                    }

                    // delete solutions
                    TargetCheckSolution::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    // delete inputs
                    TargetCheckInput::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    // delete vulnerabilities
                    TargetCheckVuln::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    // delete files
                    TargetCheckAttachment::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    $targetCheck->delete();

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

    /**
     * Control check function.
     */
    public function actionControlCustomCheck($id, $target, $category) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $form = new EntryControlForm();
            $form->attributes = $_POST["EntryControlForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            /** @var TargetCustomCheck $customCheck */
            $customCheck = TargetCustomCheck::model()->with("control")->findByAttributes(array(
                "id" => $form->id,
                "target_id" => $target->id,
            ));

            if (!$customCheck || $customCheck->control->check_category_id != $category) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $categoryCheck = TargetCheckCategory::model()->findByAttributes(array(
                "target_id" => $target->id,
                "check_category_id" => $customCheck->control->check_category_id
            ));

            if (!$categoryCheck) {
                throw new CHttpException(404, Yii::t("app", "Control not found."));
            }

            switch ($form->operation) {
                case "delete":
                    $customCheck->delete();
                    break;

                case "reset":
                    $customCheck->name = null;
                    $customCheck->background_info = null;
                    $customCheck->question = null;
                    $customCheck->result = null;
                    $customCheck->solution_title = null;
                    $customCheck->solution = null;
                    $customCheck->rating = TargetCustomCheck::RATING_NONE;
                    $customCheck->save();

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

    /**
     * Control GT check function.
     */
    public function actionGtControlCheck($id, $module, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $module = (int) $module;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->guided_test || !$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $module = ProjectGtModule::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_module_id' => $module
            ));

            if (!$module) {
                throw new CHttpException(404, Yii::t('app', 'Module not found.'));
            }

            $check = GtCheck::model()->with('check')->findByAttributes(array(
                'id' => $check,
                'gt_module_id' => $module->gt_module_id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            if ($this->_system->demo && !$check->check->demo) {
                throw new CHttpException(403, Yii::t("app", "This check is not available in the demo version."));
            }

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if (!$language) {
                $language = Language::model()->findByAttributes(array(
                    'default' => true
                ));
            }

            $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_check_id'  => $check->id
            ));

            if (!$projectCheck) {
                $projectCheck = new ProjectGtCheck();
                $projectCheck->project_id = $project->id;
                $projectCheck->gt_check_id = $check->id;
                $projectCheck->user_id = Yii::app()->user->id;
                $projectCheck->language_id = $language->id;
            }

            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $cookies = Yii::app()->request->cookies;
            $step = isset($cookies['gt_step']) ? $cookies['gt_step']->value : 0;

            switch ($model->operation) {
                case 'start':
                    if (!in_array($projectCheck->status, array(ProjectGtCheck::STATUS_OPEN, ProjectGtCheck::STATUS_FINISHED))) {
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));
                    }

                    // delete solutions
                    ProjectGtCheckSolution::model()->deleteAllByAttributes(array(
                        'project_id' => $project->id,
                        'gt_check_id' => $check->id
                    ));

                    try {
                        SystemManager::updateStatus(
                            System::STATUS_RUNNING,
                            array(System::STATUS_IDLE, System::STATUS_RUNNING)
                        );
                    } catch (Exception $e) {
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));
                    }

                    $projectCheck->status = ProjectGtCheck::STATUS_IN_PROGRESS;
                    $projectCheck->rating = ProjectGtCheck::RATING_NONE;
                    $projectCheck->started = null;
                    $projectCheck->pid = null;
                    $projectCheck->save();

                    break;

                case 'stop':
                    if ($projectCheck->status != ProjectGtCheck::STATUS_IN_PROGRESS) {
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));
                    }

                    $projectCheck->status = ProjectGtCheck::STATUS_STOP;
                    $projectCheck->save();

                    break;

                case 'reset':
                    if (!in_array($projectCheck->status, array(ProjectGtCheck::STATUS_OPEN, ProjectGtCheck::STATUS_FINISHED))) {
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));
                    }

                    // delete vulns
                    ProjectGtCheckVuln::model()->deleteAllByAttributes(array(
                        'project_id' => $project->id,
                        'gt_check_id' => $check->id
                    ));

                    // delete solutions
                    ProjectGtCheckSolution::model()->deleteAllByAttributes(array(
                        'project_id' => $project->id,
                        'gt_check_id' => $check->id
                    ));

                    // delete inputs
                    ProjectGtCheckInput::model()->deleteAllByAttributes(array(
                        'project_id' => $project->id,
                        'gt_check_id' => $check->id
                    ));

                    // delete files
                    ProjectGtCheckAttachment::model()->deleteAllByAttributes(array(
                        'project_id' => $project->id,
                        'gt_check_id' => $check->id
                    ));

                    $projectCheck->delete();

                    $response->addData('automated', $check->check->automated);
                    $response->addData('protocol', $check->check->protocol);
                    $response->addData('port', $check->check->port);

                    $inputValues = array();

                    // get default input values
                    if ($check->check->automated) {
                        $scripts = CheckScript::model()->findAllByAttributes(array(
                            'check_id' => $check->check_id
                        ));

                        $scriptIds = array();

                        foreach ($scripts as $script) {
                            $scriptIds[] = $script->id;
                        }

                        $criteria = new CDbCriteria();
                        $criteria->addInCondition("check_script_id", $scriptIds);
                        $criteria->addCondition("visible");

                        $inputs = CheckInput::model()->findAll($criteria);

                        foreach ($inputs as $input) {
                            $inputValues[] = array(
                                'id' => 'ProjectGtCheckEditForm_inputs_' . $input->id,
                                'value' => $input->value
                            );
                        }
                    }

                    $response->addData('inputs', $inputValues);

                    break;

                case 'gt-next':
                    $stepObject = $this->_getGtStep($project, $step + 1);

                    if ($stepObject) {
                        $step++;
                    } else {
                        $step = 0;
                    }

                    $response->addData('step', $step);

                    break;

                case 'gt-prev':
                    $stepObject = $this->_getGtStep($project, $step - 1);

                    if ($stepObject) {
                        $step--;
                    } else {
                        $step = 0;
                    }

                    $response->addData('step', $step);

                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Save GT check.
     */
    public function actionGtSaveCheck($id, $module, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $module = (int) $module;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->guided_test || !$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $module = ProjectGtModule::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_module_id' => $module
            ));

            if (!$module) {
                throw new CHttpException(404, Yii::t('app', 'Module not found.'));
            }

            $check = GtCheck::model()->with('check')->findByAttributes(array(
                'id' => $check,
                'gt_module_id' => $module->gt_module_id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if (!$language) {
                $language = Language::model()->findByAttributes(array(
                    'default' => true
                ));
            }

            $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_check_id'  => $check->id
            ));

            if (!$projectCheck) {
                $projectCheck = new ProjectGtCheck();
                $projectCheck->project_id = $project->id;
                $projectCheck->gt_check_id = $check->id;
                $projectCheck->user_id = Yii::app()->user->id;
                $projectCheck->language_id = $language->id;
                $projectCheck->status = ProjectGtCheck::STATUS_OPEN;
            }

            $model = new ProjectGtCheckEditForm();
            $model->attributes = $_POST['ProjectGtCheckEditForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            if (!$model->target) {
                $model->target = null;
            }

            if (!$model->protocol) {
                $model->protocol = null;
            }

            if (!$model->port) {
                $model->port = null;
            }

            if ($model->result == '') {
                $model->result = null;
            }

            $projectCheck->user_id = Yii::app()->user->id;
            $projectCheck->language_id = $language->id;
            $projectCheck->target = $model->target;
            $projectCheck->protocol = $model->protocol;
            $projectCheck->port = $model->port;
            $projectCheck->result = $model->result;
            $projectCheck->status = ProjectGtCheck::STATUS_FINISHED;
            $projectCheck->rating = $model->rating;
            $projectCheck->save();

            // delete solutions
            ProjectGtCheckSolution::model()->deleteAllByAttributes(array(
                'project_id' => $project->id,
                'gt_check_id' => $check->id
            ));

            // delete inputs
            ProjectGtCheckInput::model()->deleteAllByAttributes(array(
                'project_id' => $project->id,
                'gt_check_id' => $check->id
            ));

            // add solutions
            if ($model->solutions) {
                $hasCustom = false;

                foreach ($model->solutions as $solutionId) {
                    // reset solution
                    if (!$solutionId) {
                        break;
                    }

                    if ($solutionId == ProjectGtCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER) {
                        $hasCustom = true;
                        continue;
                    }

                    $solution = CheckSolution::model()->findByAttributes(array(
                        'id' => $solutionId,
                        'check_id' => $check->check->id
                    ));

                    if (!$solution) {
                        throw new CHttpException(404, Yii::t('app', 'Solution not found.'));
                    }

                    $solution = new ProjectGtCheckSolution();
                    $solution->project_id = $project->id;
                    $solution->check_solution_id = $solutionId;
                    $solution->gt_check_id = $check->id;
                    $solution->save();
                }

                if ($hasCustom && $model->solution) {
                    if (User::checkRole(User::ROLE_ADMIN) && $model->saveSolution) {
                        if (!$model->solutionTitle) {
                            throw new CHttpException(403, Yii::t("app", "Please specify the solution title."));
                        }

                        $criteria = new CDbCriteria();
                        $criteria->select = "MAX(sort_order) as max_sort_order";
                        $criteria->addColumnCondition(array("check_id" => $check->check->id));

                        $maxOrder = CheckSolution::model()->find($criteria);
                        $sortOrder = 0;

                        if ($maxOrder && $maxOrder->max_sort_order !== null) {
                            $sortOrder = $maxOrder->max_sort_order + 1;
                        }

                        $solution = new CheckSolution();
                        $solution->check_id = $check->check->id;
                        $solution->title = $model->solutionTitle;
                        $solution->solution = $model->solution;
                        $solution->sort_order = $sortOrder;
                        $solution->save();

                        $solutionL10n = new CheckSolutionL10n();
                        $solutionL10n->check_solution_id = $solution->id;
                        $solutionL10n->language_id = $language->id;
                        $solutionL10n->title = $model->solutionTitle;
                        $solutionL10n->solution = $model->solution;
                        $solutionL10n->save();

                        $projectSolution = new ProjectGtCheckSolution();
                        $projectSolution->project_id = $project->id;
                        $projectSolution->gt_check_id = $check->id;
                        $projectSolution->check_solution_id = $solution->id;
                        $projectSolution->save();

                        $projectCheck->solution = null;
                        $projectCheck->solution_title = null;
                        $projectCheck->save();

                        $response->addData("newSolution", array(
                            "id" => $solution->id,
                            "title" => $solution->title,
                            "solution" => $solution->solution,
                            "multipleSolutions" => $check->check->multiple_solutions,
                        ));
                    } else {
                        $projectCheck->solution = $model->solution;
                        $projectCheck->solution_title = $model->solutionTitle;
                        $projectCheck->save();
                    }
                }
            }

            // add inputs
            if ($check->check->automated) {
                // initialize all hidden inputs, if any
                foreach ($check->check->scripts as $script) {
                    foreach ($script->inputs as $hiddenInput) {
                        if (!$hiddenInput->visible) {
                            $input = new ProjectGtCheckInput();
                            $input->project_id = $project->id;
                            $input->check_input_id = $hiddenInput->id;
                            $input->gt_check_id = $check->id;
                            $input->value = $hiddenInput->value;
                            $input->save();
                        }
                    }
                }

                // visible inputs
                if ($model->inputs && $check->check->automated) {
                    foreach ($model->inputs as $inputId => $inputValue) {
                        $input = CheckInput::model()->findByAttributes(array(
                            'id' => $inputId,
                        ));

                        if (!$input || !$input->visible) {
                            throw new CHttpException(404, Yii::t('app', 'Input not found.'));
                        }

                        if ($inputValue == '') {
                            $inputValue = null;
                        }

                        $input = new ProjectGtCheckInput();
                        $input->project_id = $project->id;
                        $input->check_input_id = $inputId;
                        $input->gt_check_id = $check->id;
                        $input->value = $inputValue;
                        $input->save();
                    }
                }
            }

            $response->addData('rating', $projectCheck->rating);

            if ($project->status == Project::STATUS_OPEN) {
                $project->status = Project::STATUS_IN_PROGRESS;
                $project->save();
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * GT check autosave.
     */
    public function actionGtAutosaveCheck($id, $module, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $module = (int) $module;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->guided_test || !$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $module = ProjectGtModule::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_module_id' => $module
            ));

            if (!$module) {
                throw new CHttpException(404, Yii::t('app', 'Module not found.'));
            }

            $check = GtCheck::model()->with('check')->findByAttributes(array(
                'id' => $check,
                'gt_module_id' => $module->gt_module_id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if (!$language) {
                $language = Language::model()->findByAttributes(array(
                    'default' => true
                ));
            }

            $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_check_id'  => $check->id
            ));

            if (!$projectCheck) {
                $projectCheck = new ProjectGtCheck();
                $projectCheck->project_id = $project->id;
                $projectCheck->gt_check_id = $check->id;
                $projectCheck->user_id = Yii::app()->user->id;
                $projectCheck->language_id = $language->id;
                $projectCheck->status = ProjectGtCheck::STATUS_OPEN;
            }

            $model = new ProjectGtCheckEditForm();
            $model->attributes = $_POST['ProjectGtCheckEditForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            if ($model->result == '') {
                $model->result = null;
            }

            $projectCheck->result = $model->result;
            $projectCheck->save();

            if ($project->status == Project::STATUS_OPEN) {
                $project->status = Project::STATUS_IN_PROGRESS;
                $project->save();
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Update checks function.
     */
    public function actionUpdateChecks($id, $target, $category) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $target = Target::model()->findByAttributes(array(
                'id' => $target,
                'project_id' => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));
            }

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id' => $target->id,
                'check_category_id' => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));
            }

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            $model = new TargetCheckUpdateForm();
            $model->attributes = $_POST['TargetCheckUpdateForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $checkIds = explode(',', $model->checks);
            $criteria = new CDbCriteria();
            $criteria->addInCondition('c.check_control_id', $controlIds);
            $criteria->addInCondition('t.id', $checkIds);
            $criteria->addColumnCondition(array("t.target_id" => $target->id));
            $criteria->together = true;

            $checks = TargetCheck::model()->with(array(
                "check" => array(
                    "alias" => "c",
                )
            ))->findAll($criteria);
            $checkData = array();

            foreach ($checks as $targetCheck) {
                $time = $targetCheck->started;
                $startedText = null;

                if ($time) {
                    $started = new DateTime($time);
                    $time = mktime() - strtotime($time);
                    $user = $targetCheck->user;

                    if ($targetCheck->status != TargetCheck::STATUS_FINISHED) {
                        $startedText = Yii::t("app", "Started by {user} on {date} at {time}", array(
                            "{user}" => $user->name ? $user->name : $user->email,
                            "{date}" => $started->format("d.m.Y"),
                            "{time}" => $started->format("H:i:s"),
                        ));
                    }
                } else {
                    $time = -1;
                }

                $table = null;

                if ($targetCheck->table_result) {
                    $table = new ResultTable();
                    $table->parse($targetCheck->table_result);
                }

                $attachmentList = array();
                $attachments = TargetCheckAttachment::model()->findAllByAttributes(array(
                    "target_check_id" => $targetCheck->id
                ));

                foreach ($attachments as $attachment) {
                    $attachmentList[] = array(
                        "name" => CHtml::encode($attachment->name),
                        "path" => $attachment->path,
                        "url" => $this->createUrl('project/attachment', array('path' => $attachment->path)),
                    );
                }

                $checkData[] = array(
                    "id" => $targetCheck->id,
                    "result" => $targetCheck->result,
                    "tableResult" => $table ? $this->renderPartial("/project/target/check/tableresult", array("table" => $table), true) : "",
                    "finished" => $targetCheck->status == TargetCheck::STATUS_FINISHED,
                    "time" => $time,
                    "attachmentControlUrl" => $this->createUrl("project/controlattachment"),
                    "attachments" => $attachmentList,
                    "startedText" => $startedText,
                );
            }

            $response->addData('checks', $checkData);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Update GT check function.
     */
    public function actionGtUpdateChecks($id, $module, $check)
    {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $module = (int) $module;
            $check = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));
            }

            if (!$project->guided_test || !$project->checkPermission()) {
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            $module = ProjectGtModule::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_module_id' => $module
            ));

            if (!$module) {
                throw new CHttpException(404, Yii::t('app', 'Module not found.'));
            }

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if ($language) {
                $language = $language->id;
            }

            $check = GtCheck::model()->with(array(
                'check',
                'suggestedTargets' => array(
                    'alias' => 'sgt',
                    'joinType' => 'LEFT JOIN',
                    'on' => 'sgt.project_id = :project_id',
                    'params' => array('project_id' => $project->id),
                    'with' => array(
                        'module' => array(
                            'with' => array(
                                'l10n' => array(
                                    'alias' => 'l10n_sgt_m',
                                    'joinType' => 'LEFT JOIN',
                                    'on' => 'l10n_sgt_m.language_id = :language_id',
                                    'params' => array('language_id' => $language)
                                ),
                            )
                        )
                    )
                )
            ))->findByAttributes(array(
                'id' => $check,
                'gt_module_id' => $module->gt_module_id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            $projectCheck = ProjectGtCheck::model()->findByAttributes(array(
                'project_id' => $project->id,
                'gt_check_id'  => $check->id
            ));

            $startedText = null;

            if ($projectCheck->started) {
                $started = new DateTime($projectCheck->started);
                $now = new DateTime();

                $time = $now->format("U") - $started->format("U");
                $user = $projectCheck->user;

                if ($projectCheck->status != ProjectGtCheck::STATUS_FINISHED) {
                    $startedText = Yii::t("app", "Started by {user} on {date} at {time}", array(
                        "{user}" => $user->name ? $user->name : $user->email,
                        "{date}" => $started->format("d.m.Y"),
                        "{time}" => $started->format("H:i:s"),
                    ));
                }
            } else {
                $time = -1;
            }

            $table = null;

            if ($projectCheck->table_result) {
                $table = new ResultTable();
                $table->parse($projectCheck->table_result);
            }

            $attachmentList = array();
            $attachments = ProjectGtCheckAttachment::model()->findAllByAttributes(array(
                "project_id" => $project->id,
                "gt_check_id" => $check->id
            ));

            foreach ($attachments as $attachment) {
                $attachmentList[] = array(
                    "name" => CHtml::encode($attachment->name),
                    "path" => $attachment->path,
                    "url" => $this->createUrl('project/attachment', array('path' => $attachment->path)),
                );
            }

            $targetList = array();

            foreach ($check->suggestedTargets as $target) {
                $targetList[] = array(
                    "id" => $target->id,
                    "host" => $target->target,
                    "module" => array(
                        "name" => $target->module->localizedName,
                        "id" => $target->gt_module_id
                    )
                );
            }

            $checkData = array(
                "id" => $check->id,
                "result" => $projectCheck->result,
                "tableResult" => $table ? $this->renderPartial("/project/gt/tableresult", array("table" => $table), true) : "",
                "finished" => $projectCheck->status == ProjectGtCheck::STATUS_FINISHED,
                "time" => $time,
                "attachmentControlUrl" => $this->createUrl("project/gtcontrolattachment"),
                "attachments" => $attachmentList,
                "targetControlUrl" => $this->createUrl("project/gtcontroltarget"),
                "targets" => $targetList,
                "startedText" => $startedText,
            );

            $response->addData('check', $checkData);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Search action.
     */
    public function actionSearch()
    {
        $model        = new SearchForm();
        $projects     = array();
        $projectStats = array();

        if (isset($_POST['SearchForm']))
        {
            $model->attributes = $_POST['SearchForm'];

            if ($model->validate())
            {
                $criteria = new CDbCriteria();
                $criteria->order    = 't.deadline ASC, t.name ASC';
                $criteria->together = true;

                if (User::checkRole(User::ROLE_CLIENT))
                {
                    $user = User::model()->findByPk(Yii::app()->user->id);
                    $criteria->addColumnCondition(array( 'client_id' => $user->client_id ));
                }

                $searchCriteria = new CDbCriteria();
                $searchCriteria->addSearchCondition('t.name', $model->query, true, 'OR', 'ILIKE');
                $criteria->mergeWith($searchCriteria);

                if (User::checkRole(User::ROLE_ADMIN))
                    $projects = Project::model()->with('client')->findAll($criteria);
                else
                    $projects = Project::model()->with(array(
                        'projectUsers' => array(
                            'joinType' => 'INNER JOIN',
                            'on'       => 'projectUsers.user_id = :user_id',
                            'params'   => array(
                                'user_id' => Yii::app()->user->id,
                            ),
                        ),
                        'client'
                    ))->findAll($criteria);

                $projectStats = array();

                foreach ($projects as $project)
                {
                    $checkCount    = 0;
                    $finishedCount = 0;
                    $lowRiskCount  = 0;
                    $medRiskCount  = 0;
                    $highRiskCount = 0;

                    $targets = Target::model()->with(array(
                        'checkCount',
                        'finishedCount',
                        'lowRiskCount',
                        'medRiskCount',
                        'highRiskCount',
                    ))->findAllByAttributes(array(
                        'project_id' => $project->id
                    ));

                    foreach ($targets as $target)
                    {
                        if ($target->checkCount)
                            $checkCount += $target->checkCount;

                        if ($target->finishedCount)
                            $finishedCount += $target->finishedCount;

                        if ($target->lowRiskCount)
                            $lowRiskCount += $target->lowRiskCount;

                        if ($target->medRiskCount)
                            $medRiskCount += $target->medRiskCount;

                        if ($target->highRiskCount)
                            $highRiskCount += $target->highRiskCount;
                    }

                    $projectStats[$project->id] = array(
                        'checkCount'    => $checkCount,
                        'finishedCount' => $finishedCount,
                        'lowRiskCount'  => $lowRiskCount,
                        'medRiskCount'  => $medRiskCount,
                        'highRiskCount' => $highRiskCount
                    );
                }
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array(Yii::t('app', 'Search'), '');

		// display the page
        $this->pageTitle = Yii::t('app', 'Search');
		$this->render('search', array(
            'model'    => $model,
            'projects' => $projects,
            'stats'    => $projectStats,
            'statuses' => array(
                Project::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                Project::STATUS_OPEN        => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED    => Yii::t('app', 'Finished'),
            )
        ));
    }

    /**
     * Display a list of users.
     */
	public function actionUsers($id, $page=1)
	{
        $id   = (int) $id;
        $page = (int) $page;

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 'admin DESC, role DESC, name ASC';
        $criteria->addColumnCondition(array(
            'project_id' => $project->id
        ));

        $users = ProjectUser::model()->with('user')->findAll($criteria);

        $userCount = ProjectUser::model()->count($criteria);
        $paginator = new Paginator($userCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array($project->name, $this->createUrl('project/view', array( 'id' => $project->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Users'), '');

        // display the page
        $this->pageTitle = $project->name;
		$this->render('user/index', array(
            'project' => $project,
            'users'   => $users,
            'p'       => $paginator,
        ));
	}

    /**
     * Project user edit page.
     */
	public function actionEditUser($id, $user=0) {
        $id = (int) $id;
        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));
        }

        $newRecord = false;

        if ($user) {
            $user = ProjectUser::model()->with("user")->findByAttributes(array(
                "project_id" => $project->id,
                "user_id" => $user,
            ));

            if (!$user) {
                throw new CHttpException(404, Yii::t("app", "User not found."));
            }
        } else {
            $user = new ProjectUser();
            $newRecord = true;
        }

		$form = new ProjectUserEditForm(
            $newRecord ? ProjectUserEditForm::NEW_SCENARIO : ProjectUserEditForm::SAVE_SCENARIO,
            $project->id
        );

        if (!$newRecord) {
            $form->userId = $user->user_id;
            $form->admin = $user->admin;
            $form->hoursAllocated = $user->hours_allocated;
            $form->hoursSpent = $user->hoursSpent;
        } else {
            $form->hoursAllocated = 0.0;
            $form->hoursSpent = 0.0;
        }

		// collect user input data
		if (isset($_POST["ProjectUserEditForm"])) {
			$form->attributes = $_POST["ProjectUserEditForm"];
            $form->admin = isset($_POST["ProjectUserEditForm"]["admin"]);

			if ($form->validate()) {
                $checkUser = User::model()->findByPk($form->userId);

                if ($checkUser->role == User::ROLE_ADMIN) {
                    $form->admin = true;
                }

                if ($newRecord) {
                    $user->user_id = $form->userId;
                }

                $user->admin = $form->admin;
                $user->project_id = $project->id;
                $user->hours_allocated = $form->hoursAllocated;
                $user->save();

                Yii::app()->user->setFlash("success", Yii::t("app", "User saved."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        // find users
        $addedUsers = ProjectUser::model()->findAllByAttributes(array(
            "project_id" => $project->id
        ));

        $addedUserIds = array();

        foreach ($addedUsers as $usr) {
            $addedUserIds[] = $usr->user_id;
        }

        $criteria = new CDbCriteria();
        $criteria->addNotInCondition("id", $addedUserIds);
        $criteria->order = "t.role DESC, t.name ASC, t.email ASC";

        $roleCriteria = new CDbCriteria();
        $roleCriteria->addColumnCondition(array(
            "role" => User::ROLE_CLIENT,
            "client_id" => $project->client_id
        ));
        $roleCriteria->addInCondition("role", array(User::ROLE_USER, User::ROLE_ADMIN), "OR");
        $criteria->mergeWith($roleCriteria);
        $users = User::model()->findAll($criteria);

        $title = $newRecord ? Yii::t("app", "New User") : ($user->user->name ? $user->user->name : $user->user->email);
        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Users"), $this->createUrl("project/users", array("id" => $project->id)));
        $this->breadcrumbs[] = array($title, "");

		// display the page
        $this->pageTitle = $title;
		$this->render("user/edit", array(
            "form" => $form,
            "project" => $project,
            "user" => $user,
            "users" => $users,
        ));
	}

    /**
     * Control user function.
     */
    public function actionControlUser($id)
    {
        $response = new AjaxResponse();

        try
        {
            $id = (int) $id;

            $project = Project::model()->findByPk($id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $user = ProjectUser::model()->findByAttributes(array(
                'project_id' => $project->id,
                'user_id'    => $model->id
            ));

            if ($user === null)
                throw new CHttpException(404, Yii::t('app', 'User not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $user->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Vulnerabilities.
     */
    public function actionVulns($id, $page=1)
    {
        $id   = (int) $id;
        $page = (int) $page;

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if (!$project->checkPermission())
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $targets = Target::model()->findAllByAttributes(array(
            'project_id' => $project->id
        ));

        $targetIds = array();

        foreach ($targets as $target)
            $targetIds[] = $target->id;

        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.target_id', $targetIds);
        $criteria->addInCondition('t.rating', array(
            TargetCheck::RATING_HIGH_RISK,
            TargetCheck::RATING_MED_RISK,
            TargetCheck::RATING_LOW_RISK
        ));
        $criteria->order = 'target.host, "check".name';
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->together = true;

        $checks = TargetCheck::model()->with(array(
            'check' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    ),
                    'control' => array(
                        'with' => array(
                            'l10n' => array(
                                'alias'    => 'l10n_co',
                                'joinType' => 'LEFT JOIN',
                                'on'       => 'l10n_co.language_id = :language_id',
                                'params'   => array( 'language_id' => $language )
                            ),
                            'category' => array(
                                'with' => array(
                                    'l10n' => array(
                                        'alias'    => 'l10n_ca',
                                        'joinType' => 'LEFT JOIN',
                                        'on'       => 'l10n_ca.language_id = :language_id',
                                        'params'   => array( 'language_id' => $language )
                                    ),
                                )
                            )
                        )
                    )
                ),
            ),
            'vuln' => array(
                'with' => 'user'
            ),
            'target',
        ))->findAll($criteria);

        $checkCount = TargetCheck::model()->count($criteria);
        $paginator  = new Paginator($checkCount, $page);

        Yii::app()->user->returnUrl = $this->createUrl("project/vulns", array("id" => $project->id, "page" => $page));
        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Vulns"), "");

        // display the page
        $this->pageTitle = $project->name;
		$this->render("vuln/index", array(
            "project" => $project,
            "checks" => $checks,
            "p" => $paginator,
            "ratings" => TargetCheck::getRatingNames(),
            "statuses" => array(
                TargetCheckVuln::STATUS_OPEN => Yii::t("app", "Open"),
                TargetCheckVuln::STATUS_RESOLVED => Yii::t("app", "Resolved"),
            ),
        ));
    }

    /**
     * Vulnerability edit page.
     */
	public function actionEditVuln($id, $target, $check) {
        $id = (int) $id;
        $target = (int) $target;
        $check = (int) $check;
        $newRecord = false;

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $check = TargetCheck::model()->with(array(
            "check" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "l10n.language_id = :language_id",
                        "params" => array("language_id" => $language)
                    ),
                ),
            )
        ))->findByAttributes(array(
            "check_id" => $check,
            "target_id" => $target
        ));

        if (!$check || !in_array($check->rating, array(TargetCheck::RATING_LOW_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_HIGH_RISK))) {
            throw new CHttpException(404, Yii::t("app", "Check not found."));
        }

        $vuln = TargetCheckVuln::model()->findByAttributes(array(
            "target_check_id" => $check->id,
        ));

        if (!$vuln) {
            $vuln = new TargetCheckVuln();
            $vuln->target_check_id = $check->id;
            $newRecord = true;
        }

		$model = new VulnEditForm();

        if (!$newRecord) {
            $model->status = $vuln->status;
            $model->userId = $vuln->user_id;
            $model->deadline = $vuln->deadline;
        } else {
            $model->deadline = date("Y-m-d");
        }

		// collect user input data
		if (isset($_POST["VulnEditForm"])) {
			$model->attributes = $_POST["VulnEditForm"];

            if (!$model->userId) {
                $model->userId = null;
            }

			if ($model->validate()) {
                $vuln->status = $model->status;
                $vuln->user_id = $model->userId;
                $vuln->deadline = $model->deadline;
                $vuln->save();

                Yii::app()->user->setFlash("success", Yii::t("app", "Vulnerability saved."));
                $project->refresh();
                $this->redirect(Yii::app()->user->returnUrl);
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array( "id" => $project->id )));
        $this->breadcrumbs[] = array(Yii::t("app", "Vulns"), $this->createUrl("project/vulns", array( "id" => $project->id )));
        $this->breadcrumbs[] = array($check->check->localizedName, "");

        $admins = User::model()->findAllByAttributes(array(
            "role" => User::ROLE_ADMIN
        ));

        $excludeIds = array();

        foreach ($admins as $admin) {
            $excludeIds[] = $admin->id;
        }

        $clients = User::model()->findAllByAttributes(array(
            "role" => User::ROLE_CLIENT,
            "client_id" => $project->client_id
        ));

        foreach ($clients as $client) {
            $excludeIds[] = $client->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            "project_id" => $project->id
        ));
        $criteria->order = "name ASC, email ASC";

        if (count($excludeIds)) {
            $criteria->addNotInCondition("user_id", $excludeIds);
        }

        $users = ProjectUser::model()->with("user")->findAll($criteria);

		// display the page
        $this->pageTitle = $check->check->localizedName;
		$this->render("vuln/edit", array(
            "model" => $model,
            "project" => $project,
            "admins" => $admins,
            "users" => $users,
            "statuses" => array(
                TargetCheckVuln::STATUS_OPEN => Yii::t("app", "Open"),
                TargetCheckVuln::STATUS_RESOLVED => Yii::t("app", "Resolved"),
            )
        ));
	}

    /**
     * Upload custom attachment function.
     */
    function actionUploadCustomAttachment($id, $target, $category, $check) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $category = (int) $category;
            $check = (int) $check;
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $category = TargetCheckCategory::model()->with("category")->findByAttributes(array(
                "target_id"         => $target->id,
                "check_category_id" => $category
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t("app", "Category not found."));
            }

            $controls = CheckControl::model()->findAllByAttributes(array(
                "check_category_id" => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            $customCheck = TargetCustomCheck::model()->findByPk($check);

            if (!$customCheck || !in_array($customCheck->check_control_id, $controlIds)) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $model = new TargetCustomCheckAttachmentUploadForm();
            $model->attachment = CUploadedFile::getInstanceByName("TargetCustomCheckAttachmentUploadForm[attachment]");

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $attachment = new TargetCustomCheckAttachment();
            $attachment->target_custom_check_id = $customCheck->id;
            $attachment->name = $model->attachment->name;
            $attachment->type = $model->attachment->type;
            $attachment->size = $model->attachment->size;
            $attachment->path = hash("sha256", $attachment->name . rand() . time());
            $attachment->save();

            $model->attachment->saveAs(Yii::app()->params["attachments"]["path"] . "/" . $attachment->path);

            $response->addData("name", CHtml::encode($attachment->name));
            $response->addData("url", $this->createUrl("project/customattachment", array("path" => $attachment->path)));
            $response->addData("path", $attachment->path);
            $response->addData("controlUrl", $this->createUrl("project/controlcustomattachment"));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Get custom attachment.
     */
    public function actionCustomAttachment($path) {
        $attachment = TargetCustomCheckAttachment::model()->with(array(
            "targetCustomCheck" => array(
                "with" => array(
                    "target" => array(
                        "with" => "project"
                    )
                )
            )
        ))->findByAttributes(array(
            "path" => $path
        ));

        if ($attachment === null) {
            throw new CHttpException(404, Yii::t("app", "Attachment not found."));
        }

        if (!$attachment->targetCustomCheck->target->project->checkPermission()) {
            throw new CHttpException(403, Yii::t("app", "Access denied."));
        }

        $filePath = Yii::app()->params["attachments"]["path"] . "/" . $attachment->path;

        if (!file_exists($filePath)) {
            throw new CHttpException(404, Yii::t("app", "Attachment not found."));
        }

        // give user a file
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . str_replace("\"", "", $attachment->name) . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . $attachment->size);

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Control custom attachment.
     */
    public function actionControlCustomAttachment() {
        $response = new AjaxResponse();

        try {
            $model = new TargetCustomCheckAttachmentControlForm();
            $model->attributes = $_POST["TargetCustomCheckAttachmentControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $path = $model->path;
            $attachment = TargetCustomCheckAttachment::model()->with(array(
                "targetCustomCheck" => array(
                    "with" => array(
                        "target" => array(
                            "with" => "project"
                        )
                    )
                )
            ))->findByAttributes(array(
                "path" => $path
            ));

            if ($attachment === null) {
                throw new CHttpException(404, Yii::t("app", "Attachment not found."));
            }

            if (!$attachment->targetCustomCheck->target->project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            switch ($model->operation) {
                case "delete":
                    $attachment->delete();
                    @unlink(Yii::app()->params["attachments"]["path"] . "/" . $attachment->path);
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

    /**
     * Project track time page.
     */
	public function actionTrackTime($id) {
        $id = (int) $id;
        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        if (!$project->checkPermission()) {
            throw new CHttpException(403, Yii::t("app", "Access denied."));
        }

		$form = new ProjectTimeForm();
        $user = ProjectUser::model()->with("user")->findByAttributes(array(
            "user_id" => Yii::app()->user->id,
            "project_id" => $project->id,
        ));

        if (!$user) {
            throw new CHttpException(403, Yii::t("app", "User should be added to the project to be able to track time."));
        }

		// collect user input data
		if (isset($_POST["ProjectTimeForm"])) {
			$form->attributes = $_POST["ProjectTimeForm"];

			if ($form->validate()) {
                $record = new ProjectTime();
                $record->user_id = $user->user->id;
                $record->project_id = $project->id;
                $record->hours = $form->hoursSpent;
                $record->description = $form->description;
                $record->save();

                Yii::app()->user->setFlash("success", Yii::t("app", "User saved."));
                $this->redirect(array("project/view", "id" => $project->id));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Time"), $this->createUrl("project/time", array("id" => $project->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Track"), "");

		// display the page
        $this->pageTitle = Yii::t("app", "Track Time");
		$this->render("time/track", array(
            "form" => $form,
            "project" => $project,
            "user" => $user,
        ));
	}

    /**
     * Control target category function.
     */
    public function actionControlCategory($id, $target) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            if (!$project->checkPermission()) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $target = Target::model()->findByAttributes(array(
                "id" => $target,
                "project_id" => $project->id
            ));

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $category = TargetCheckCategory::model()->findByAttributes(array(
                "target_id" => $target->id,
                "check_category_id" => $model->id,
            ));

            if (!$category) {
                throw new CHttpException(404, Yii::t("app", "Category not found."));
            }

            switch ($model->operation)             {
                case "delete":
                    $controls = CheckControl::model()->findAllByAttributes(array(
                        "check_category_id" => $category->check_category_id
                    ));

                    $controlIds = array();

                    foreach ($controls as $control) {
                        $controlIds[] = $control->id;
                    }

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("check_control_id", $controlIds);
                    $checks = Check::model()->findAll($criteria);
                    $checkIds = array();

                    foreach ($checks as $check) {
                        $checkIds[] = $check->id;
                    }

                    $criteria = new CDbCriteria();
                    $criteria->addColumnCondition(array("target_id" => $target->id));
                    $criteria->addInCondition("check_id", $checkIds);
                    TargetCheck::model()->deleteAll($criteria);
                    $category->delete();

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
