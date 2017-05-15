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
            "checkUser + control, edittarget, controltarget, uploadattachment, controlattachment, controlcheck, updatechecks, savecheck, copycheck, time, tracktime, controlcategory",
            "checkAdmin + edit, users, edituser, controluser, controltime",
            "ajaxOnly + savecheck, savecustomcheck, controlattachment, controlcheck, updatechecks, controluser, copycheck, controlchecklist, check, controlcategory",
            "postOnly + savecheck, savecustomcheck, uploadattachment, controlattachment, controlcheck, updatechecks, controluser, copycheck, controlchecklist, check, controlcategory",
            "idle",
		);
	}

    /**
     * Get project's quick targets
     * @param $projectId
     * @param $languageId
     * @return array|mixed|null
     */
    private function _getQuickTargets($projectId, $languageId) {
        $baseCriteria = new CDbCriteria();
        $baseCriteria->addCondition("t.project_id = :project_id");
        $baseCriteria->params = ["project_id" => $projectId];
        $baseCriteria->together = true;

        $domainCriteria = clone $baseCriteria;
        $ipCriteria = clone $baseCriteria;

        $domainCriteria->addCondition("t.host !~ '^(\d{1,3}\.){3}\d{1,3}$'");
        $domainCriteria->order = "t.host ASC";

        $ipCriteria->addCondition("host ~ '^(\d{1,3}\.){3}\d{1,3}$'");
        $ipCriteria->order = "t.host::inet";

        $with = [
            "categories" => [
                "with" => [
                    "l10n" => [
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => ["language_id" => $languageId]
                    ],
                ],
                "order" => "categories.name",
            ]
        ];

        return array_merge(
            Target::model()->with($with)->findAll($ipCriteria),
            Target::model()->with($with)->findAll($domainCriteria)
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
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
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
        ));
	}

    /**
     * Display a standard project page
     */
    private function _viewStandard($project, $page) {
        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
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

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('project/index'));
        $this->breadcrumbs[] = array($project->name, '');

        // display the page
        $this->pageTitle = $project->name;
		$this->render('view', array(
            "project"  => $project,
            "targets"  => $targets,
            "p"        => $paginator,
            "ratings" => TargetCheck::getRatingNames(),
            "columns" => array(
                TargetCheck::COLUMN_TARGET          => Yii::t("app", "Target"),
                TargetCheck::COLUMN_NAME            => Yii::t("app", "Name"),
                TargetCheck::COLUMN_REFERENCE       => Yii::t("app", "Reference"),
                TargetCheck::COLUMN_SOLUTION        => Yii::t("app", "Solution"),
                TargetCheck::COLUMN_RATING          => Yii::t("app", "Rating"),
            ),
            "quickTargets" => $this->_getQuickTargets($project->id, $language),
            "checks" => Check::model()->findAll()
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

        $this->_viewStandard($project, $page);
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
        $model->hiddenFields = [];

        if (!$newRecord) {
            $model->name = $project->name;
            $model->year = $project->year;
            $model->status = $project->status;
            $model->clientId = $project->client_id;
            $model->deadline = $project->deadline;
            $model->startDate = $project->start_date ? $project->start_date : date("Y-m-d");
            $model->hoursAllocated = $project->hours_allocated;

            if ($project->language) {
                $model->languageId = $project->language->id;
            }

            $fields = GlobalCheckField::model()->findAll();

            foreach ($fields as $f) {
                if ($project->isFieldHidden($f->name)) {
                    $model->hiddenFields[] = $f->name;
                }
            }
        } else {
            $model->year = date("Y");
            $model->deadline = date("Y-m-d");
            $model->startDate = date("Y-m-d");
        }

		// collect user input data
		if (isset($_POST["ProjectEditForm"])) {
			$model->attributes = $_POST["ProjectEditForm"];
            $model->hiddenFields = isset($_POST["ProjectEditForm"]["hiddenFields"]) ? $_POST["ProjectEditForm"]["hiddenFields"] : [];

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

                if ($model->languageId) {
                    $language = Language::model()->findByPk($model->languageId);

                    if (!$language) {
                        throw new CHttpException(404, "Language not found.");
                    }

                    $project->language_id = $language->id;
                } else {
                    $project->language_id = null;
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

                $criteria = new CDbCriteria();
                $criteria->join = "INNER JOIN target_checks tc ON tc.id = t.target_check_id";
                $criteria->join .= sprintf(" INNER JOIN targets tr ON tr.id = tc.target_id AND tr.project_id = %d", $project->id);
                $criteria->join .= " INNER JOIN check_fields cf ON cf.id = t.check_field_id";
                $criteria->join .= " INNER JOIN global_check_fields g ON g.id = cf.global_check_field_id";

                $hiddenFields = array_map(function ($item) {
                    return "'$item'";
                }, $model->hiddenFields);

                $criteria->join .=  sprintf(" AND g.name IN (%s)", count($hiddenFields) ? implode(",", $hiddenFields) : "''");

                $hiddenFields = TargetCheckField::model()->findAll($criteria);
                $hiddenFieldIds = [];

                foreach ($hiddenFields as $f) {
                    $hiddenFieldIds[] = $f->id;
                }

                // make not hidden removed checkboxes
                $criteria = new CDbCriteria();
                $criteria->addNotInCondition("id", $hiddenFieldIds);
                TargetCheckField::model()->updateAll(["hidden" => false], $criteria);

                // make hidden added fields
                $criteria = new CDbCriteria();
                $criteria->addInCondition("id", $hiddenFieldIds);
                TargetCheckField::model()->updateAll(["hidden" => true], $criteria);

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
            "fields" => GlobalCheckField::model()->findAll(["order" => "sort_order ASC"]),
            "languages" => Language::model()->findAll()
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
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
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
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
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
     * Display a list of check categories.
     */
	public function actionTarget($id, $target, $page=1) {
        $id = (int) $id;
        $target = (int) $target;
        $page = (int) $page;

        $project = Project::model()->with(array(
            "userHoursAllocated",
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
        $criteria->limit = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
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
                        ),
                    ),
                ),
            ),
        ))->findAll($newCriteria);

        $categoryCount = TargetCheckCategory::model()->count($criteria);
        $paginator = new Paginator($categoryCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array( "id" => $project->id )));
        $this->breadcrumbs[] = array($target->hostPort, "");

        // display the page
        $this->pageTitle = $target->hostPort . ($target->description ? " / " . $target->description : "");
		$this->render("target/index", array(
            "project" => $project,
            "target" => $target,
            "categories" => $categories,
            "p" => $paginator,
            "quickTargets" => $this->_getQuickTargets($project->id, $language),
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
        $model->templateIds = array();

        if (!$newRecord) {
            $model->host = $target->host;
            $model->port = $target->port;
            $model->description = $target->description;
            $model->sourceType = $target->check_source_type;
            $model->relationTemplateId = $target->relation_template_id;

            $categories = $target->_categories;

            foreach ($categories as $category) {
                $model->categoryIds[] = $category->check_category_id;
            }

            $templates = TargetChecklistTemplate::model()->findAllByAttributes(array(
                    "target_id" => $target->id,
                )
            );

            foreach ($templates as $template) {
                $model->templateIds[] = $template->checklist_template_id;
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
            $model->templateIds = array();
			$model->attributes = $_POST["TargetEditForm"];

			if ($model->validate()) {
                $target->project_id = $project->id;
                $target->host = $model->host;
                $target->port = $model->port;
                $target->description = $model->description;
                $target->check_source_type = $model->sourceType;

                if (!$model->relationTemplateId) {
                    $target->relation_template_id = null;
                    $target->relations = null;
                } else {
                    if ($target->relation_template_id != $model->relationTemplateId) {
                        $target->relation_template_id = $model->relationTemplateId;
                        $template = RelationTemplate::model()->findByPk($model->relationTemplateId);

                        if (!$template) {
                            throw new CHttpException(404, "Template not found.");
                        }

                        $target->relations = $template->relations;
                    }
                }

                $target->save();

                $addCategories = array();
                $delCategories = array();
                $addTemplates  = array();
                $delTemplates  = array();
                $addReferences = array();
                $delReferences = array();

                if (!$newRecord) {
                    $oldCategories = array();
                    $oldReferences = array();
                    $oldTemplates = array();

                    switch ($model->sourceType) {
                        case Target::SOURCE_TYPE_CHECK_CATEGORIES:
                            // fill in addCategories & delCategories arrays
                            $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                                "target_id"          => $target->id,
                                "checklist_template" => false
                            ));

                            foreach ($categories as $category) {
                                $oldCategories[] = $category->check_category_id;
                            }

                            $delCategories= array_diff($oldCategories, $model->categoryIds);
                            $addCategories = array_diff($model->categoryIds, $oldCategories);

                            TargetCheckCategory::model()->deleteAllByAttributes(array(
                                "target_id"          => $target->id,
                                "checklist_template" => true
                            ));
                            TargetChecklistTemplate::model()->deleteAllByAttributes(array(
                                "target_id" => $target->id
                            ));

                            $model->templateIds = array();

                            break;

                        case Target::SOURCE_TYPE_CHECKLIST_TEMPLATES:
                            // fill in addTemplates & delTemplates arrays
                            $templates = TargetChecklistTemplate::model()->findAllByAttributes(array(
                                "target_id"           => $target->id,
                            ));

                            foreach ($templates as $template) {
                                $oldTemplates[] = $template->checklist_template_id;
                            }

                            $delTemplates = array_diff($oldTemplates, $model->templateIds);
                            $addTemplates = array_diff($model->templateIds, $oldTemplates);

                            TargetCheckCategory::model()->deleteAllByAttributes(array(
                                "target_id"           => $target->id,
                                "checklist_template"  => false,
                            ));

                            $model->categoryIds = array();

                            break;
                    }

                    // fill in addReferences & delReferences arrays
                    $references = TargetReference::model()->findAllByAttributes(array(
                        "target_id" => $target->id
                    ));

                    foreach ($references as $reference) {
                        $oldReferences[] = $reference->reference_id;
                    }

                    $delReferences = array_diff($oldReferences, $model->referenceIds);
                    $addReferences = array_diff($model->referenceIds, $oldReferences);
                } else {
                    $addCategories = $model->categoryIds;
                    $addReferences = $model->referenceIds;
                    $addTemplates  = $model->templateIds;
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
                    $targetCategory->save();
                }

                // delete templates
                if ($delTemplates) {
                    $criteria = new CDbCriteria();
                    $criteria->addColumnCondition(array(
                        "target_id" => $target->id
                    ));
                    $criteria->addInCondition("checklist_template_id", $delTemplates);
                    $checklistTemplates = TargetChecklistTemplate::model()->findAll($criteria);

                    foreach ($checklistTemplates as $clTemplate) {
                        $categories = $clTemplate->checklistTemplate->checkCategories;
                        $delTemplateCategories = array();

                        foreach ($categories as $category) {
                            $delTemplateCategories[] = $category->id;
                        }

                        TargetCheckCategory::model()->deleteAllByAttributes(array(
                            "target_id"          => $target->id,
                            "check_category_id"  => $delTemplateCategories,
                            "checklist_template" => true,
                            "template_count"     => 1
                        ));

                        $updateCriteria = new CDbCriteria();
                        $updateCriteria->addColumnCondition(array(
                            "target_id"          => $target->id,
                            "checklist_template" => true,
                        ));
                        $updateCriteria->addInCondition("check_category_id", $delTemplateCategories);
                        $updateCriteria->addCondition("template_count > 1");

                        TargetCheckCategory::model()->updateCounters(array(
                            "template_count" => -1
                        ), $updateCriteria);
                    }

                    TargetChecklistTemplate::model()->deleteAll($criteria);
                }

                // add templates
                foreach ($addTemplates as $template) {
                    $checklistTemplate = new TargetChecklistTemplate();
                    $checklistTemplate->target_id = $target->id;
                    $checklistTemplate->checklist_template_id = $template;
                    $checklistTemplate->save();

                    $categories = $checklistTemplate->checklistTemplate->checkCategories;

                    foreach ($categories as $category) {
                        $cat = TargetCheckCategory::model()->findByAttributes(array(
                            "target_id"          => $target->id,
                            "check_category_id"  => $category->id,
                            "checklist_template" => true,
                        ));

                        if ($cat) {
                            $cat->template_count += 1;
                            $cat->save();
                            continue;
                        }

                        $cat = new TargetCheckCategory();
                        $cat->target_id              = $target->id;
                        $cat->check_category_id      = $category->id;
                        $cat->checklist_template     = true;
                        $cat->save();
                    }
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

                Yii::app()->user->setFlash("success", Yii::t("app", "Target saved."));

                $target->refresh();
                ReindexJob::enqueue(["target_id" => $target->id]);
                HostResolveJob::enqueue([
                    "targets" => [$target->id]
                ]);

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

        $templateCategories = ChecklistTemplateCategory::model()->with(array(
            "templates" => array(
                "alias" => "tpl",
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array(
                            "language_id" => $language
                        )
                    )
                )
            )
        ))->findAllByAttributes(
            array(),
            array("order" => "t.name")
        );
        $templateCount = ChecklistTemplate::model()->count();

        $relationTemplates = RelationTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array(
                    "language_id" => $language
                )
            )
        ))->findAllByAttributes(
                array(),
                array("order" => "t.name")
            );

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
            "templateCount" => $templateCount,
            "templateCategories" => $templateCategories,
            "relationTemplates" => $relationTemplates,
            "references" => $references
        ));
	}

    /**
     * Add list of targets page
     * @param $id
     * @throws Exception
     */
    public function actionAddTargetList($id) {
        $form = new TargetListAddForm();
        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new Exception("Project not found.");
        }

        if (isset($_POST["TargetListAddForm"])) {
            $form->attributes = $_POST["TargetListAddForm"];

            if ($form->validate()) {
                $ids = [];

                try {
                    $targets = trim($form->targetList);
                    $targets = explode("\n", $targets);

                    foreach ($targets as $target) {
                        $host = trim($target);
                        
                        $t = new Target();
                        $t->project_id = $project->id;
                        $t->host = $host;
                        $t->save();

                        $t->refresh();
                        $ids[] = $t->id;
                    }
                } catch (Exception $e) {}

                $references = Reference::model()->findAll();

                // by default all references must be bound to target
                foreach ($ids as $tId) {
                    foreach ($references as $ref) {
                        $targetRef = new TargetReference();
                        $targetRef->target_id = $tId;
                        $targetRef->reference_id = $ref->id;
                        $targetRef->save();
                    }
                }

                HostResolveJob::enqueue([
                    "targets" => $ids
                ]);

                Yii::app()->user->setFlash("success", Yii::t("app", "Targets added."));
                $this->redirect(array("project/view", "id" => $project->id));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "New Targets"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "New Targets");
        $this->render("target/new-list", array(
            "model" => $form,
        ));
    }

    /**
     * Import targets from file
     * @throws Exception
     */
    public function actionImportTarget($id) {
        $form = new TargetImportForm();
        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new Exception("Project not found.");
        }

        if (isset($_POST["TargetImportForm"])) {
            $form->attributes = $_POST["TargetImportForm"];
            $form->file = CUploadedFile::getInstanceByName("TargetImportForm[file]");
            $success = true;

            if ($form->validate()) {
                try {
                    if ($form->type == ImportManager::TYPE_NESSUS) {
                        $nrm = new NessusReportManager();

                        if ($form->mappingId) {
                            $mapping = NessusMapping::model()->findByPk($form->mappingId);

                            if (!$mapping) {
                                throw new CHttpException(404, "Nessus mapping not found.");
                            }
                        } else {
                            $parsed = $nrm->parse($form->file->tempName);
                            $mapping = ImportManager::importMapping($parsed);
                        }

                        $filename = md5($project->id . time() . rand());
                        $filepath = Yii::app()->params["tmpPath"] . DS . $filename;
                        $form->file->saveAs($filepath);
                        $project->import_filename = $filename;
                        $project->save();

                        $this->redirect($this->createUrl("project/editmapping", ["id" => $project->id, "mId" => $mapping->id]));
                    } else {
                        ImportManager::importTargets($form->file->tempName, $form->type, $project);
                    }
                } catch (ImportFileParsingException $e) {
                    $form->addError("file", Yii::t("app", "File parsing error."));
                    $success = false;
                } catch (NoValidTargetException $e) {
                    $form->addError("file", Yii::t("app", "File doesn't contain any valid targets."));
                    $success = false;
                } catch (InvalidNessusReportException $e) {
                    $form->addError("file", $e->getMessage());
                    $success = false;
                } catch (CHttpException $e) {
                    throw $e;
                }
            } else {
                $success = false;
            }

            if ($success) {
                Yii::app()->user->setFlash("success", Yii::t("app", "Import completed."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $mappings = NessusMapping::model()->findAll();

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Import Target"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Import From File");
        $this->render("target/import", array(
            "model" => $form,
            "types" => ImportManager::$types,
            "mappings" => $mappings
        ));
    }

    /**
     * Edit created mapping before imports
     * @param $id
     * @param $mId
     * @throws CHttpException
     * @throws Exception
     */
    public function actionEditMapping($id, $mId) {
        $id = (int) $id;
        $mId = (int) $mId;

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new Exception("Project not found.");
        }

        $mapping = NessusMapping::model()->findByPk($mId);

        if (!$mapping) {
            throw new Exception("Mapping not found.");
        }

        $ratings = TargetCheck::getValidRatings();
        $nessusRatings = NessusReportManager::$ratings;

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Import"), $this->createUrl("project/importTarget", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Mapping"), ""];

        // display the page
        $this->pageTitle = Yii::t("app", "Configure Mapping");

        $this->render("target/edit-mapping", [
            "project" => $project,
            "mapping" => $mapping,
            "ratings" => $ratings,
            "nessusRatings" => $nessusRatings
        ]);
    }

    /**
     * Apply mapping to project
     * @throws CHttpException
     */
    public function actionApplyMapping() {
        $form = new ProjectApplyMappingForm();

        if (!isset($_POST["ProjectApplyMappingForm"])) {
            $this->redirect(["project/index"]);

            return;
        }

        $form->attributes = $_POST["ProjectApplyMappingForm"];
        $success = true;

        try {
            if (!$form->validate()) {
                throw new FormValidationException();
            }

            $project = Project::model()->findByPk($form->projectId);

            if (!$project) {
                throw new CHttpException(404, "Project not found.");
            }

            $mapping = NessusMapping::model()->findByPk($form->mappingId);

            if (!$mapping) {
                throw new CHttpException(404, "Mapping not found");
            }

            $pm = new ProjectManager();
            $pm->importNessusReport($project, $mapping);

            $project->import_filename = null;
            $project->save();

            if ($success) {
                Yii::app()->user->setFlash("success", Yii::t("app", "Import completed."));

                $this->redirect($this->createUrl("project/view", ["id" => $project->id]));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        } catch (FormValidationException $f) {
            Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
        } catch (Exception $e) {
            Yii::log($e->getMessage() . "\n" . $e->getTraceAsString());
            Yii::app()->user->setFlash("error", Yii::t("app", "Mapping error."));
        }

        $mappings = NessusMapping::model()->findAll();

        $form = new TargetImportForm();
        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [Yii::t("app", "Import"), ""];

        // display the page
        $this->pageTitle = Yii::t("app", "Import From File");
        $this->render("target/import", [
            "model" => $form,
            "types" => ImportManager::$types,
            "mappings" => $mappings
        ]);
    }

    /**
     * Target check chain edit page
     * @param $id
     * @param $target
     * @throws CHttpException
     */
    public function actionEditChain($id, $target) {
        $id = (int) $id;
        $target = (int) $target;

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        if (!$project->checkPermission()) {
            throw new CHttpException(403, Yii::t("app", "Access denied."));
        }

        $target = Target::model()->findByPk($target);

        if (!$target) {
            throw new CHttpException(404, Yii::t("app", "Target not found."));
        }

        $model = new TargetChainEditForm();
        $model->relations = $target->relations;

        // collect user input data
        if (isset($_POST["TargetChainEditForm"])) {
            $model->attributes = $_POST["TargetChainEditForm"];
            $success = true;

            if ($model->validate()) {
                try {
                    RelationManager::validateRelations($model->relations, $target);
                } catch (Exception $e) {
                    $model->addError("relations", $e->getMessage());
                    $success = false;
                }
            }

            if ($success) {
                $target->relations = $model->relations;
                $target->save();

                Yii::app()->user->setFlash("success", Yii::t("app", "Target saved."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $categories = CheckCategory::model()->findAll();
        $filters = RelationManager::$filters;
        $activeCheck = null;

        try {
            RelationManager::validateRelations($target->relations, $target);
            $relations = new SimpleXMLElement($target->relations, LIBXML_NOERROR);
            $cellId = (int) TargetManager::getChainLastCellId($target->id);

            if ($cellId) {
                $cell = RelationManager::getCell($relations, $cellId);
                $activeCheck = $cell->attributes()->label;
            }
        } catch (Exception $e) {}

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array($project->name, $this->createUrl("project/view", array("id" => $project->id)));
        $this->breadcrumbs[] = array($target->hostPort, $this->createUrl("project/target", array("id" => $project->id, "target" => $target->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Edit Check Chain"), "");

        // display the page
        $this->pageTitle = $target->hostPort;
        $this->render("target/chain/edit", array(
            "model" => $model,
            "project" => $project,
            "categories" => $categories,
            "filters" => $filters,
            "target" => $target,
            "activeCheck" => $activeCheck,
        ));
    }

    /**
     * Control target's check chain
     * @param $id
     * @param $target
     */
    public function actionControlChain($id, $target) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $target = (int) $target;
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

            RelationManager::validateRelations($target->relations, $target);

            switch ($model->operation) {
                case "start":
                    if ($target->isChainRunning) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    $targetChecks = TargetCheck::model()->findAllByAttributes(array(
                        "target_id" => $target->id
                    ));

                    foreach ($targetChecks as $tc) {
                        $tc->rating = TargetCheck::RATING_NONE;
                        $tc->save();
                    }

                    ChainJob::enqueue(array(
                        "target_id" => $target->id,
                        "operation" => ChainJob::OPERATION_START,
                    ));

                    break;

                case "stop":
                    if (!$target->isChainRunning) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    ChainJob::enqueue(array(
                        "target_id" => $target->id,
                        "operation" => ChainJob::OPERATION_STOP,
                    ));

                    break;

                case "reset":
                    ChainJob::enqueue(array(
                        "target_id" => $target->id,
                        "operation" => ChainJob::OPERATION_STOP,
                        "reset"     => true
                    ));

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }

            StatsJob::enqueue(array(
                "target_id" => $target->id,
            ));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Returns chain status / active check / chain messages
     * @param $id
     * @param $target
     */
    public function actionChainMessages($id, $target) {
        $response = new AjaxResponse();

        $id = (int) $id;
        $target = (int) $target;
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

        $response->addData("status", TargetManager::getChainStatus($target->id));
        $activeCheck = null;

        try {
            RelationManager::validateRelations($target->relations, $target);
            $relations = new SimpleXMLElement($target->relations, LIBXML_NOERROR);
            $cellId = (int) TargetManager::getChainLastCellId($target->id);

            if ($cellId) {
                $cell = RelationManager::getCell($relations, $cellId);
                $name = (string)$cell->attributes()->label;
            }
        } catch (Exception $e) {}

        if (isset($cellId) && $cellId) {
            $response->addData("check", ["id" => $cellId, "name" => $name]);
        }

        $response->addData("messages", TargetManager::getChainMessages());

        echo $response->serialize();
    }

    /**
     * Returns link to check
     * @param $target
     * @param $check
     * @throws CHttpException
     */
    public function actionCheckLink() {
        $response = new AjaxResponse();

        try {
            $model = new TargetCheckLinkForm();
            $model->attributes = $_POST["TargetCheckLinkForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        $target = $model->target;
        $target = Target::model()->findByPk($target);

        if (!$target) {
            throw new CHttpException(404, "Target not found.");
        }

        $check = $model->check;
        $check = Check::model()->findByPk($check);


        if (!$check) {
            throw new CHttpException(404, "Check not found.");
        }

        $targetCheck = TargetCheck::model()->findByAttributes(array(
            "target_id" => $target->id,
            "check_id" => $check->id
        ));

        if (!$targetCheck) {
            throw new CHttpException(404, "Target check not found.");
        }

        $url = $this->createUrl("project/checks", array(
            "id" => $target->project->id,
            "target" => $target->id,
            "category" => $check->control->category->id,
            "controlToOpen" => $check->control->id,
            "checkToOpen" => $targetCheck->id
        ));

        $response->addData("url", $url);

        echo $response->serialize();
    }

    /**
     * Display a list of checks.
     */
	public function actionChecks($id, $target, $category, $controlToOpen=0, $checkToOpen=0) {
        $id = (int) $id;
        $target = (int) $target;
        $category = (int) $category;
        $controlToOpen = (int) $controlToOpen;
        $checkToOpen = (int) $checkToOpen;

        $project = Project::model()->with(array(
            "userHoursAllocated",
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
                        "joinType" => "INNER JOIN",
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
            "category" => $category,
            "controls" => $controls,
            "ratings" => TargetCheck::getRatingNames(),
            "stats" => $stats,
            "quickTargets" => $this->_getQuickTargets($project->id, $language),
            "controlToOpen" => $controlToOpen,
            "checkToOpen" => $checkToOpen,
            "fields" => GlobalCheckField::model()->findAll()
        ));
	}

    /**
     * Get running checks list
     * @param $id
     * @param $target
     */
    public function actionRunningChecks() {
        $response = new AjaxResponse();

        try {
            if (isset($_POST["RunningChecksForm"])) {
                $targetId = $_POST["RunningChecksForm"]["target_id"];
            }

            $target = Target::model()->findByPk($targetId);

            if (!$target) {
                throw new CHttpException(404, Yii::t("app", "Target not found."));
            }

            $checkIds = [];

            foreach ($target->targetChecks as $tc) {
                if ($tc->isRunning) {
                    $time = TargetCheckManager::getStartTime($tc->id);

                    $checkIds[] = TargetCheckManager::getData($tc);
                }
            }

            $response->addData("checks", $checkIds);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
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

            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array("t.check_control_id" => $control->id));

            if ($target->check_source_type == Target::SOURCE_TYPE_CHECK_CATEGORIES) {
                $referenceIds = array();
                $references = TargetReference::model()->findAllByAttributes(array(
                    "target_id" => $target->id
                ));

                foreach ($references as $reference) {
                    $referenceIds[] = $reference->reference_id;
                }
                $criteria->addInCondition("t.reference_id", $referenceIds);
            }

            $criteria->order = "t.sort_order ASC, tc.id ASC";

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
     * Render check form
     * @param TargetCheck $targetCheck
     * @param TargetCheckCategory $category
     * @param Language $language
     * @param bool $issue
     * @return mixed|string
     * @throws CException
     * @throws Exception
     */
    public function renderCheckForm(TargetCheck $targetCheck, TargetCheckCategory $category, Language $language, $issue = false) {
        $target = $targetCheck->target;
        $project = $target->project;
        $check = $targetCheck->check;

        $lang = array(
            "l10n" => array(
                "on" => "l10n.language_id = :language_id",
                "params" => array("language_id" => $language->id)
            )
        );

        if (!count($targetCheck->scripts)) {
            foreach ($check->scripts as $script) {
                $targetCheckScript = new TargetCheckScript();
                $targetCheckScript->check_script_id = $script->id;
                $targetCheckScript->target_check_id = $targetCheck->id;
                $targetCheckScript->save();
            }
        }

        $targetCheck->refresh();

        foreach ($targetCheck->scripts as $script) {
            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array(
                "visible" => true,
                "check_script_id" => $script->script->id,
            ));
            $criteria->order = 'sort_order ASC';

            $script->script->inputs = CheckInput::model()->with(array_merge(array(
                "targetInputs" => array(
                    "alias" => "ti",
                    "on" => "ti.target_check_id = :tc_id",
                    "params" => array("tc_id" => $targetCheck->id),
                )
            ),
                $lang
            ))->findAll($criteria);
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("check_id" => $targetCheck->check->id));
        $criteria->order = "sort_order ASC";

        $results = CheckResult::model()->with($lang)->findAll($criteria);
        $solutions = CheckSolution::model()->with($lang)->findAll($criteria);

        $params = [
            "project" => $project,
            "target" => $target,
            "category" => $category,
            "check" => $targetCheck,
            "fields" => $targetCheck->getOrderedFields(),
            "checkData" => $check,
            "results" => $results,
            "solutions" => $solutions,
            "ratings" => TargetCheck::getRatingNames(),
        ];

        if (!$issue) {
            $params["goToNext"] = true;
        }

        return $this->renderPartial("partial/check-form", $params, true);
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
            $check = TargetCheck::model()->findByAttributes(array(
                "id" => $check,
                "target_id" => $target->id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            $html = $this->renderCheckForm($check, $category, $language);

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

            if ($model->tableResult == "") {
                $model->tableResult = null;
            }

            if ($model->scripts) {
                foreach ($model->scripts as $scriptData) {
                    $dataEncoded = json_decode($scriptData);
                    $script = CheckScript::model()->findByPk($dataEncoded->id);

                    if (!$script) {
                        throw new CHttpException(404, "Script not found.");
                    }

                    $tcs = TargetCheckScript::model()->findByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                        "check_script_id" => $script->id
                    ));

                    if (!$tcs) {
                        throw new CHttpException(404, "Target check has no such script.");
                    }

                    $tcs->start = (bool) $dataEncoded->start;
                    $tcs->save();
                }
            } else {
                // Set all scripts to start if no script data received
                TargetCheckScript::model()->updateAll(
                    array("start" => true),
                    "target_check_id = :target_check_id",
                    array(
                        "target_check_id" => $targetCheck->id
                    )
                );
            }

            if ($model->timeouts) {
                foreach ($model->timeouts as $timeoutData) {
                    $dataEncoded = json_decode($timeoutData);
                    $script = CheckScript::model()->findByPk($dataEncoded->script_id);

                    if (!$script) {
                        throw new CHttpException(404, "Script not found.");
                    }

                    $tcs = TargetCheckScript::model()->findByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                        "check_script_id" => $script->id
                    ));

                    if (!$tcs) {
                        throw new CHttpException(404, "Target check has no such script.");
                    }

                    $tcs->timeout = $dataEncoded->timeout ? $dataEncoded->timeout : null;
                    $tcs->save();
                }
            }

            $targetCheck->user_id = Yii::app()->user->id;
            $targetCheck->language_id = $language->id;
            $targetCheck->status = TargetCheck::STATUS_FINISHED;
            $targetCheck->rating = $model->rating;

            if (User::checkRole(User::ROLE_ADMIN) && $model->saveResult) {
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
                $result->result = $model->result;
                $result->sort_order = $sortOrder;
                $result->save();

                $resultL10n = new CheckResultL10n();
                $resultL10n->check_result_id = $result->id;
                $resultL10n->language_id = $language->id;
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
            }

            $targetCheck->table_result = $model->tableResult;
            $targetCheck->last_modified = $model->last_modified;
            $targetCheck->save();

            // delete old solutions
            TargetCheckSolution::model()->deleteAllByAttributes(array(
                "target_check_id" => $targetCheck->id,
            ));

            // delete old inputs
            TargetCheckInput::model()->deleteAllByAttributes(array(
                "target_check_id" => $targetCheck->id,
            ));

            $targetCheck->vuln_user_id = null;
            $targetCheck->vuln_deadline = null;
            $targetCheck->vuln_status = null;

            foreach ($targetCheck->fields as $field) {
                $value = isset($model->fields[$field->name]) ? $model->fields[$field->name] : null;
                $field->setValue($value);
            }

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


                        $targetCheck->setFieldValue(GlobalCheckField::FIELD_SOLUTION, null);
                        $targetCheck->setFieldValue(GlobalCheckField::FIELD_SOLUTION_TITLE, null);
                        $targetCheck->last_modified = $model->last_modified;
                        $targetCheck->save();
                    } else {
                        $targetCheck->setFieldValue(GlobalCheckField::FIELD_SOLUTION, $model->solution);
                        $targetCheck->setFieldValue(GlobalCheckField::FIELD_SOLUTION_TITLE, $model->solutionTitle);
                        $targetCheck->last_modified = $model->last_modified;
                        $targetCheck->save();
                    }
                }
            }

            if (count($model->attachmentTitles)) {
                foreach ($model->attachmentTitles as $title) {
                    $decodedTitle = json_decode($title);

                    $attachment = TargetCheckAttachment::model()->findByAttributes(array(
                        "path" => $decodedTitle->path,
                        "target_check_id" => $targetCheck->id,
                    ));

                    if (!$attachment) {
                        throw new CHttpException(404, 'Attachment not found.');
                    }

                    $attachment->title = $decodedTitle->title;
                    $attachment->save();
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

            StatsJob::enqueue(array(
                "category_id" => $targetCheck->check->control->check_category_id,
                "target_id" => $targetCheck->target_id
            ));

            $response->addData("targetCheck", array(
                'id' => $targetCheck->id,
                "check" => array(
                    "id" => $check->id
                )
            ));

            $response->addData("rating", $targetCheck->rating);
            $response->addData("last_modified", $targetCheck->last_modified);

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

            $targetCheck->last_modified = $model->last_modified;
            $targetCheck->save();

            if ($project->status == Project::STATUS_OPEN) {
                $project->status = Project::STATUS_IN_PROGRESS;
                $project->save();
            }

            StatsJob::enqueue(array(
                "category_id" => $targetCheck->check->control->check_category_id,
                "target_id" => $targetCheck->target_id,
            ));
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

            if (!$form->solutionTitle) {
                $form->solutionTitle = null;
            }

            if ($form->createCheck) {
                $cm = new CheckManager();
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

                $check = new Check();
                $now = new DateTime();
                $check->create_time = $now->format(ISO_DATE_TIME);
                $check->name = $form->name;
                $check->check_control_id = $control->id;
                $check->reference_id = $reference->id;
                $check->reference_code = "CHECK-" . $customCheck->reference;
                $check->reference_url = $reference->url;
                $check->automated = false;
                $check->multiple_solutions = false;
                $check->status = Check::STATUS_INSTALLED;
                $check->save();

                $check->sort_order = $check->id;
                $check->save();

                $checkL10n = new CheckL10n();
                $checkL10n->check_id = $check->id;
                $checkL10n->language_id = $language->id;
                $checkL10n->name = $form->name;
                $checkL10n->save();

                $cm->reindexFields($check);

                foreach ($check->fields as $field) {
                    $formField = Utils::camelize($field->name);

                    if (in_array($field->name, GlobalCheckField::$system) && isset($form->{$formField})) {
                        foreach (Language::model()->findAll() as $l) {
                            $field->setValue($form->{$formField}, $l->id);
                        }
                    }
                }

                $tcm = new TargetCheckManager();

                $targetCheck = $tcm->create($check, [
                    "target_id" => $target->id,
                    "user_id" => Yii::app()->user->id,
                    "language_id" => $language->id,
                    "status" => TargetCheck::STATUS_FINISHED,
                    "rating" => $form->rating
                ]);

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

                StatsJob::enqueue(array(
                    "category_id" => $targetCheck->check->control->check_category_id,
                    "target_id" => $targetCheck->target_id,
                ));
            } else {
                $customCheck->user_id = Yii::app()->user->id;
                $customCheck->name = $form->name;
                $customCheck->background_info = $form->backgroundInfo;
                $customCheck->question = $form->question;
                $customCheck->result = $form->result;
                $customCheck->solution_title = $form->solutionTitle;
                $customCheck->solution = $form->solution;
                $customCheck->rating = $form->rating;

                if (count($form->attachmentTitles)) {
                    foreach ($form->attachmentTitles as $title) {
                        $decodedTitle = json_decode($title);

                        $attachment = TargetCustomCheckAttachment::model()->findByAttributes(array(
                            "path" => $decodedTitle->path,
                            "target_custom_check_id" => $customCheck->id,
                        ));

                        if (!$attachment) {
                            throw new CHttpException(404, 'Attachment not found.');
                        }

                        $attachment->title = $decodedTitle->title;
                        $attachment->save();
                    }
                }

                $customCheck->last_modified = $form->last_modified;
                $customCheck->save();

                $response->addData("rating", $customCheck->rating);
                $response->addData("last_modified", $customCheck->last_modified);

                StatsJob::enqueue(array(
                    "category_id" => $customCheck->control->check_category_id,
                    "target_id" => $customCheck->target_id,
                ));
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
            $attachment->title = $model->attachment->name;
            $attachment->type = $model->attachment->type;
            $attachment->size = $model->attachment->size;
            $attachment->path = hash('sha256', $attachment->name . rand() . time());
            $attachment->save();

            $model->attachment->saveAs(Yii::app()->params['attachments']['path'] . '/' . $attachment->path);

            $response->addData('name', CHtml::encode($attachment->name));
            $response->addData('title', CHtml::encode($attachment->title));
            $response->addData('url', $this->createUrl('project/attachment', array( 'path' => $attachment->path )));
            $response->addData('path', $attachment->path);
            $response->addData('controlUrl', $this->createUrl('project/controlattachment'));
            $response->addData('targetCheck', $targetCheck->id);
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

            switch ($model->operation) {
                case "start":
                    if ($targetCheck->isRunning) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    // delete solutions
                    TargetCheckSolution::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    $targetCheck->rating = TargetCheck::RATING_NONE;
                    $targetCheck->save();

                    TargetCheckManager::start($targetCheck->id);

                    break;

                case "stop":
                    if (!$targetCheck->isRunning) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    TargetCheckManager::stop($targetCheck->id);

                    break;

                case "reset":
                    if ($targetCheck->isRunning) {
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

                    $targetCheck->vuln_user_id = null;
                    $targetCheck->vuln_deadline = null;
                    $targetCheck->vuln_status = null;

                    // delete files
                    TargetCheckAttachment::model()->deleteAllByAttributes(array(
                        "target_check_id" => $targetCheck->id,
                    ));

                    $targetCheck->target_file = null;
                    $targetCheck->status = TargetCheck::STATUS_OPEN;
                    $targetCheck->result_file = null;
                    $targetCheck->table_result = null;
                    $targetCheck->save();

                    $response->addData("automated", $check->automated);
                    $response->addData("protocol",  $check->applicationProtocol);
                    $response->addData("port", $check->port);
                    $inputValues = array();
                    $timeoutValues = array();
                    $startScripts = array();

                    // reset fields
                    foreach ($targetCheck->fields as $field) {
                        $field->reset();
                    }

                    // get default input values
                    if ($check->automated) {
                        $criteria = new CDbCriteria();
                        $criteria->addCondition("target_check_id = :target_check_id");
                        $criteria->params = array(
                            "target_check_id" => $targetCheck->id
                        );
                        TargetCheckScript::model()->updateAll(
                            array(
                                "start"   => true,
                                "timeout" => null,
                            ),
                            $criteria
                        );

                        $scripts = TargetCheckScript::model()->findAll($criteria);

                        foreach ($scripts as $script) {
                            $timeout = $script->timeout ? $script->timeout : $script->script->package->timeout;
                            $timeoutValues[] = array(
                                "id"      => "TargetCheckEditForm_" . $targetCheck->id . "_timeouts_" . $script->script->id,
                                "timeout" => $timeout,
                            );
                            $startScripts[] = array(
                                "id"    => "TargetCheckEditForm_" . $targetCheck->id . "_scripts_" . $script->script->id,
                                "start" => (bool) $script->start,
                            );
                        }

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
                    $response->addData("timeouts", $timeoutValues);
                    $response->addData("scripts", $startScripts);

                    break;

                case "copy":
                    $tcm = new TargetCheckManager();
                    $copy = $tcm->create($targetCheck->check, [
                        "target_id" => $targetCheck->target_id,
                        "user_id" => Yii::app()->user->id,
                        "language_id" => $language->id,
                    ]);

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

            StatsJob::enqueue(array(
                "category_id" => $category->check_category_id,
                "target_id" => $target->id,
            ));
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
                    $customCheck->solution_title = null;
                    $customCheck->solution = null;
                    $customCheck->rating = TargetCustomCheck::RATING_NONE;
                    $customCheck->save();

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }

            StatsJob::enqueue(array(
                "category_id" => $category,
                "target_id" => $target->id,
            ));
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
                $checkData[] = TargetCheckManager::getData($targetCheck);
            }

            $response->addData('checks', $checkData);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Update issue
     * @param $id
     * @param $issue
     */
    public function actionUpdateIssueChecks($id, $issue) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $issue = (int) $issue;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            $issue = Issue::model()->findByPk($issue);

            if (!$issue) {
                throw new CHttpException(404, Yii::t("app", "Issue not found."));
            }

            $evidences = $issue->evidences;
            $targetCheckIds = [];

            foreach ($evidences as $e) {
                $targetCheckIds[] = $e->targetCheck->id;
            }

            $form = new TargetCheckUpdateForm();
            $form->attributes = $_POST["TargetCheckUpdateForm"];

            if (!$form->validate()) {
                $errorText = '';

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $checkIds = explode(",", $form->checks);

            foreach ($checkIds as $cid) {
                if (!in_array($cid, $targetCheckIds)) {
                    throw new Exception(Yii::t("app", "Evidence not found."));
                }
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition("t.id", $checkIds);
            $checks = TargetCheck::model()->findAll($criteria);
            $checkData = [];

            foreach ($checks as $targetCheck) {
                $checkData[] = TargetCheckManager::getData($targetCheck);
            }

            $response->addData("checks", $checkData);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Get issue running checks
     * @param $id
     * @param $issue
     */
    public function actionIssueRunningChecks($id, $issue) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $issue = (int) $issue;

            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            $issue = Issue::model()->findByPk($issue);

            if (!$issue) {
                throw new CHttpException(404, Yii::t("app", "Issue not found."));
            }

            $evidences = $issue->evidences;
            $targetCheckIds = [];

            foreach ($evidences as $e) {
                $targetCheckIds[] = $e->targetCheck->id;
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition("t.id", $targetCheckIds);

            $checks = TargetCheck::model()->findAll($criteria);
            $checkIds = [];

            foreach ($checks as $tc) {
                if ($tc->isRunning) {
                    $checkIds[] = TargetCheckManager::getData($tc);
                }
            }

            $response->addData("checks", $checkIds);
        } catch (Exception $e) {
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

        if (isset($_POST["SearchForm"]))
        {
            $model->attributes = $_POST["SearchForm"];

            if ($model->validate())
            {
                $criteria = new CDbCriteria();
                $criteria->order    = "t.deadline ASC, t.name ASC";
                $criteria->together = true;

                if (User::checkRole(User::ROLE_CLIENT))
                {
                    $user = User::model()->findByPk(Yii::app()->user->id);
                    $criteria->addColumnCondition(array( "client_id" => $user->client_id ));
                }

                $searchCriteria = new CDbCriteria();
                $searchCriteria->addSearchCondition("t.name", $model->query, true, "OR", "ILIKE");
                $criteria->mergeWith($searchCriteria);

                if (User::checkRole(User::ROLE_ADMIN))
                    $projects = Project::model()->with("client")->findAll($criteria);
                else
                    $projects = Project::model()->with(array(
                        "projectUsers" => array(
                            "joinType" => "INNER JOIN",
                            "on"       => "projectUsers.user_id = :user_id",
                            "params"   => array(
                                "user_id" => Yii::app()->user->id,
                            ),
                        ),
                        "client"
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
                        "checkCount",
                        "finishedCount",
                        "lowRiskCount",
                        "medRiskCount",
                        "highRiskCount",
                    ))->findAllByAttributes(array(
                        "project_id" => $project->id
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
                        "checkCount"    => $checkCount,
                        "finishedCount" => $finishedCount,
                        "lowRiskCount"  => $lowRiskCount,
                        "medRiskCount"  => $medRiskCount,
                        "highRiskCount" => $highRiskCount
                    );
                }
            }
            else
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Projects"), $this->createUrl("project/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Search"), "");

		// display the page
        $this->pageTitle = Yii::t("app", "Search");
		$this->render("search", array(
            "model"    => $model,
            "projects" => $projects,
            "stats"    => $projectStats,
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
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
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
                $this->redirect(array("project/edituser", "id" => $project->id, "user" => $user->user_id));
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
            $attachment->title = $model->attachment->name;
            $attachment->type = $model->attachment->type;
            $attachment->size = $model->attachment->size;
            $attachment->path = hash("sha256", $attachment->name . rand() . time());
            $attachment->save();

            $model->attachment->saveAs(Yii::app()->params["attachments"]["path"] . "/" . $attachment->path);

            $response->addData("name", CHtml::encode($attachment->name));
            $response->addData("title", CHtml::encode($attachment->title));
            $response->addData("url", $this->createUrl("project/customattachment", array("path" => $attachment->path)));
            $response->addData("path", $attachment->path);
            $response->addData("controlUrl", $this->createUrl("project/controlcustomattachment"));
            $response->addData("customCheck", $customCheck->id);
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
                $record->time = $form->hoursSpent * 3600;

                // Set start_time by user hours
                $now = new DateTime();
                $record->create_time = $now->format(ISO_DATE_TIME);
                $now->sub(new DateInterval(sprintf("PT%sH", $form->hoursSpent)));
                $record->start_time = $now->format(ISO_DATE_TIME);
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

    /**
     * Action search checks for binding the issue
     * @param $id
     */
    public function actionSearchChecks($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new Exception(Yii::t("app", "Project not found."));
            }

            if (!isset($_POST["SearchForm"])) {
                throw new Exception(Yii::t("app", "Invalid search query"));
            }

            $form = new SearchForm();
            $form->attributes = $_POST["SearchForm"];

            if (!$form->validate()) {
                $errorText = '';

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $cm = new CheckManager();
            $language = Language::model()->findByAttributes([
                "code" => Yii::app()->language
            ]);
            $projectIssues = Issue::model()->findAllByAttributes([
                "project_id" => $project->id
            ]);
            $exclude = [];

            foreach ($projectIssues as $issue) {
                $exclude[] = $issue->check->id;
            }

            $checks = $cm->filter($form->query, $language->id, $exclude);
            $data = [];

            foreach ($checks as $check) {
                $data[] = $check->check->serialize($language->id);
            }

            $response->addData("checks", $data);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Add issue to project
     * @param $id
     * @throws CHttpException
     * @throws Exception
     */
    public function actionAddIssue($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;

            /** @var Project $project */
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new Exception(Yii::t("app", "Project not found."));
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

            $id = $form->id;

            /** @var Check $check */
            $check = Check::model()->findByPk($id);

            if (!$check) {
                throw new CHttpException(404, Yii::t("app", "Check not found."));
            }

            switch ($form->operation) {
                case "add":
                    $pm = new ProjectManager();
                    $issue = $pm->addIssue($project, $check);

                    $response->addData("url", $this->createUrl("project/issue", [
                        "id" => $project->id,
                        "issue" => $issue->id
                    ]));

                    break;

                default:
                    throw new Exception(Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Project issue list
     * @param $id
     * @param int $page
     * @throws CHttpException
     */
    public function actionIssues($id, $page=1) {
        $page = (int) $page;
        $id = (int) $id;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, "Project not found.");
        }

        $language = Language::model()->findByAttributes([
            "code" => Yii::app()->language
        ]);

        if ($language) {
            $language = $language->id;
        }

        $offset = ($page - 1) * $this->entriesPerPage;
        $issues = $project->getIssues($offset);
        $issueCount = count($issues);

        $paginator = new Paginator($issueCount, $page);

        $affectedAssetCount = [];

        /** @var Issue $issue */
        foreach ($issues as $issue) {
            $count = 0;
            $targetCache = [];

            /** @var IssueEvidence $evidence */
            foreach ($issue->evidences as $evidence) {
                $target = $evidence->targetCheck->target_id;

                if (!in_array($target, $targetCache)) {
                    $targetCache[] = $target;
                    $count++;
                }
            }

            $affectedAssetCount[$issue->id] = $count;
        }

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Issues"), ""];

        // display the page
        $this->pageTitle = Yii::t("app", "Issues");
        $this->render("issue/index", array(
            "project" => $project,
            "issues" => $issues,
            "p" => $paginator,
            "quickTargets" => $this->_getQuickTargets($project->id, $language),
            "affectedAssetCount" => $affectedAssetCount,
        ));
    }

    /**
     * Issue view action
     * @param $id
     * @param $issue
     * @throws CHttpException
     */
    public function actionIssue($id, $issue) {
        $id = (int) $id;
        $issue = (int) $issue;

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        $issue = Issue::model()->findByPk($issue);

        if (!$issue) {
            throw new CHttpException(404, Yii::t("app", "Issue not found."));
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $ips = [];
        $evidences = IssueEvidence::model()->with([
            "targetCheck" => [
                "with" => "target"
            ]
        ])->findAllByAttributes(["issue_id" => $issue->id]);

        $evidenceGroups = [];

        foreach ($evidences as $evidence) {
            $target = $evidence->targetCheck->target;
            $name = $target->ip ? $target->ip : $target->host;

            if ($name) {
                $ips[] = $name;
            }

            if (!isset($evidenceGroups[$name])) {
                $evidenceGroups[$name] = [];
            }

            $evidenceGroups[$name][] = $evidence;
        }

        $title = $issue->check->localizedName;
        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Issues"), $this->createUrl("project/issues", ["id" => $project->id])];
        $this->breadcrumbs[] = [$title, ""];

        // display the page
        $this->pageTitle = $title;
        $this->render("issue/view", [
            "project" => $project,
            "issue" => $issue,
            "check" => $issue->check,
            "quickTargets" => $this->_getQuickTargets($project->id, $language),
            "evidenceGroups" => $evidenceGroups
        ]);
    }

    /**
     * Control issue
     */
    public function actionControlIssue() {
        $response = new AjaxResponse();

        try {
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

            $id = $form->id;
            $issue = Issue::model()->findByPk($id);

            if ($issue === null) {
                throw new CHttpException(404, Yii::t("app", "Issue not found."));
            }

            switch ($form->operation) {
                case "delete":
                    $issue->delete();
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
     * Search targets
     * @param $id
     * @param $issue
     */
    public function actionSearchTargets($id, $issue) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $issue = (int) $issue;

            $project = Project::model()->findByPk($id);

            if (!$project){
                throw new CHttpException(404, Yii::t("app", "Project not found."));
            }

            $issue = Issue::model()->findByPk($issue);

            if (!$issue) {
                throw new CHttpException(404, Yii::t("app", "Issue not found."));
            }

            if (!isset($_POST["SearchForm"])) {
                throw new CHttpException(400, Yii::t("app", "Invalid search query"));
            }

            $form = new SearchForm();
            $form->attributes = $_POST["SearchForm"];

            if (!$form->validate()) {
                $errorText = '';

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $tm = new TargetManager();

            $exclude = [];

            foreach ($issue->evidences as $evidence) {
                if (!in_array($evidence->targetCheck->target_id, $exclude)) {
                    $exclude[] = $evidence->targetCheck->target_id;
                }
            }

            $targets = $tm->filter($form->query, $project->id);
            $data = [];

            /** @var Target $target */
            foreach ($targets as $target) {
                $data[] = $target->serialize();
            }

            $response->addData("targets", $data);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Evidence page
     * @param $id
     * @param $issue
     * @param $evidence
     * @throws CHttpException
     */
    public function actionEvidence($id, $issue, $evidence) {
        $id = (int) $id;
        $issue = (int) $issue;
        $evidence = (int) $evidence;

        $language = Language::model()->findByAttributes([
            "code" => Yii::app()->language
        ]);

        if (!$language) {
            $language = Language::model()->findByAttributes([
                "default" => true
            ]);
        }

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        $issue = Issue::model()->with([
            "check" => [
                "with" => [
                    "l10n" => [
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => ["language_id" => $language->id]
                    ]
                ]
            ]
        ])->findByPk($issue);

        if (!$issue) {
            throw new CHttpException(404, Yii::t("app", "Issue not found."));
        }

        $evidence = IssueEvidence::model()->findByPk($evidence);

        if (!$evidence) {
            throw new CHttpException(404, Yii::t("app", "Evidence not found."));
        }

        $targetCheck = $evidence->targetCheck;
        $title = $targetCheck->target->getName();

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Issues"), $this->createUrl("project/issues", ["id" => $project->id])];
        $this->breadcrumbs[] = [
            $issue->check->localizedName,
            $this->createUrl("project/issue", ["id" => $project->id, "issue" => $issue->id])
        ];
        $this->breadcrumbs[] = [$title, ""];

        // display the page
        $this->pageTitle = $title;
        $this->render("issue/evidence/edit", [
            "project" => $project,
            "issue" => $issue,
            "evidence" => $evidence,
            "targetCheck" => $targetCheck,
            "language" => $language,
            "quickTargets" => $this->_getQuickTargets($project->id, $language->id),
        ]);
    }

    /**
     * Add evidence to issue
     * @param $id
     * @param $issue
     * @throws CHttpException
     * @throws Exception
     */
    public function actionAddEvidence($id, $issue) {
        $response = new AjaxResponse();
        $tm = new TargetManager();

        try {
            $id = (int) $id;
            $issue = (int) $issue;
            $project = Project::model()->findByPk($id);

            if (!$project) {
                throw new Exception(Yii::t("app", "Project not found."));
            }

            /** @var Issue $issue */
            $issue = Issue::model()->findByAttributes([
                "id" => $issue,
                "project_id" => $project->id,
            ]);

            if (!$issue) {
                throw new Exception(Yii::t("app", "Issue not found."));
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

            $id = $form->id;

            /** @var Target $target */
            $target = Target::model()->findByAttributes([
                "id" => $id,
                "project_id" => $project->id,
            ]);

            if (!$target) {
                throw new Exception(Yii::t("app", "Target not found."));
            }

            switch ($form->operation) {
                case "add":
                    $tm->addEvidence($target, $issue);
                    break;

                default:
                    throw new Exception(Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control evidence
     */
    public function actionControlEvidence() {
        $response = new AjaxResponse();

        try {
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

            $id = $form->id;
            $evidence = IssueEvidence::model()->findByPk($id);

            if ($evidence === null) {
                throw new CHttpException(404, Yii::t("app", "Evidence not found."));
            }

            switch ($form->operation) {
                case "delete":
                    $evidence->delete();
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
