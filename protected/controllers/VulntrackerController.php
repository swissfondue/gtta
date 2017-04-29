<?php

/**
 * Vulnerability tracker controller.
 */
class VulntrackerController extends Controller {
    private $_allowedRiskValues = array(
        TargetCheck::RATING_HIGH_RISK,
        TargetCheck::RATING_MED_RISK,
        TargetCheck::RATING_LOW_RISK
    );

    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            'https',
			'checkAuth',
            "idle",
        );
	}

    /**
     * Display a list of projects.
     */
	public function actionIndex() {
        $model = new ProjectSelectForm();

        if (isset($_POST['ProjectSelectForm']))
        {
            $model->attributes = $_POST['ProjectSelectForm'];

            if ($model->validate())
            {
                $this->redirect(array('vulntracker/vulns', 'id' => $model->projectId));
                return;
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN))
        {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project)
                if (!in_array($project->project->client_id, $clientIds))
                    $clientIds[] = $project->project->client_id;

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Tracker'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Vulnerability Tracker');
		$this->render('index', array(
            'clients' => $clients,
            'model'   => $model,
        ));
	}

    /**
     * Project vulnerabilities
     * @param $project
     * @param $page
     */
    private function _projectVulns($project, $language, $page) {
        $targets = Target::model()->findAllByAttributes(array(
            'project_id' => $project->id
        ));

        $targetIds = array();

        foreach ($targets as $target) {
            $targetIds[] = $target->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.target_id', $targetIds);
        $criteria->addInCondition('t.rating', $this->_allowedRiskValues);
        $criteria->order = 'target.host ASC, COALESCE(l10n.name, "check".name) ASC';
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->together = true;

        $targetCheckRelations = array(
            'check' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on' => 'l10n.language_id = :language_id',
                        'params' => array( 'language_id' => $language )
                    ),
                ),
            ),
            'target',
        );

        $targetChecks = TargetCheck::model()->with($targetCheckRelations)->findAll($criteria);
        $tCheckCount = count($targetChecks);
        $totalCheckCount = (int) TargetCheck::model()->count($criteria);

        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.target_id', $targetIds);
        $criteria->addInCondition('t.rating', $this->_allowedRiskValues);
        $criteria->order = 'target.host ASC';
        $criteria->together = true;

        $totalCheckCount += (int) TargetCustomCheck::model()->count($criteria);

        $epp = $this->entriesPerPage;

        if ($epp <= 0) {
            $epp = 1000000000;
        }

        $limit = 0;
        $offset = 0;

        if ($tCheckCount == $epp) {
            return array($targetChecks, array(), $totalCheckCount);
        } elseif ($tCheckCount < $epp && $tCheckCount > 0) {
            $limit = $epp - $tCheckCount;
            $offset = 0;
        } elseif ($tCheckCount == 0) {
            $limit = $epp;
            $offset = ($page - 1) * $epp - $totalCheckCount;
        }

        if ($limit && $offset) {
            $criteria->limit = $limit;
            $criteria->offset = $offset;
        }

        $customChecks = TargetCustomCheck::model()->with(array(
            'target',
        ))->findAll($criteria);

        return array($targetChecks, $customChecks, $totalCheckCount);
    }

    /**
     * Vulnerabilities.
     */
    public function actionVulns($id, $page=1) {
        $id   = (int) $id;
        $page = (int) $page;

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));
        }

        if (!$project->checkPermission()) {
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        list($checks, $customChecks, $checkCount) = $this->_projectVulns($project, $language, $page);

        $paginator = new Paginator($checkCount, $page);
        Yii::app()->user->returnUrl = $this->createUrl('vulntracker/vulns', array('id' => $project->id, 'page' => $page));

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Tracker'), $this->createUrl('vulntracker/index'));
        $this->breadcrumbs[] = array($project->name, '');

        // display the page
        $this->pageTitle = $project->name;
		$this->render('vulns', array(
            'project' => $project,
            'checks' => $checks,
            'customChecks' => $customChecks,
            'checkCount' => $checkCount,
            'p' => $paginator,
            'ratings' => TargetCheck::getRatingNames(),
            'statuses' => array(
                TargetCheck::STATUS_VULN_OPEN => Yii::t('app', 'Open'),
                TargetCheck::STATUS_VULN_RESOLVED => Yii::t('app', 'Resolved'),
            ),
        ));
    }

    /**
     * Project edit
     * @param $project
     * @param $language
     * @param $target
     * @param $check
     */
    private function _projectEdit($project, $language, $target, $check, $customCheck = false) {
        $newRecord = false;

        if ($customCheck) {
            $check = TargetCustomCheck::model()->findByPk($check);
        } else {
            $check = TargetCheck::model()->with(array(
                "check" => array(
                    "with" => array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params"   => array( "language_id" => $language )
                        ),
                    ),
                )
            ))->findByAttributes(array(
                    "check_id"  => $check,
                    "target_id" => $target
                ));
        }


        if (!$check || !in_array($check->rating, $this->_allowedRiskValues)) {
            throw new CHttpException(404, Yii::t("app", "Check not found."));
        }

        $model = new VulnEditForm();

        $model->status = $check->vuln_status;
        $model->userId = $check->vuln_user_id;
        $model->deadline = $check->vuln_deadline ? $check->vuln_deadline : date('Y-m-d');
        $model->check = $check;

		// collect user input data
		if (isset($_POST['VulnEditForm'])) {
			$model->attributes = $_POST['VulnEditForm'];

            if (!$model->userId) {
                $model->userId = null;
            }

			if ($model->validate()) {
                $check->vuln_status = $model->status;
                $check->vuln_user_id = $model->userId;
                $check->vuln_deadline = $model->deadline;

                $check->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Vulnerability saved.'));

                $project->refresh();

                $this->redirect(Yii::app()->user->returnUrl);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        if ($customCheck) {
            return array($check->name, $model);
        }

        return array($check->check->localizedName, $model);
    }

    /**
     * Vulnerability edit page.
     */
	public function actionEdit($id, $target, $check, $type) {
        $id = (int) $id;
        $target = (int) $target;
        $check = (int) $check;

        $project = Project::model()->findByPk($id);

        if (!$project) {
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));
        }

        if (!$project->checkAdmin()) {
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        list($checkName, $model) = $this->_projectEdit($project, $language, $target, $check, $type == TargetCustomCheck::TYPE);

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Tracker'), $this->createUrl('vulntracker/index'));
        $this->breadcrumbs[] = array($project->name, $this->createUrl('vulntracker/vulns', array('id' => $project->id)));
        $this->breadcrumbs[] = array($checkName, '');

        $admins = User::model()->findAllByAttributes(array(
            'role' => User::ROLE_ADMIN
        ));

        $excludeIds = array();

        foreach ($admins as $admin) {
            $excludeIds[] = $admin->id;
        }

        $clients = User::model()->findAllByAttributes(array(
            'role' => User::ROLE_CLIENT,
            'client_id' => $project->client_id
        ));

        foreach ($clients as $client) {
            $excludeIds[] = $client->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            'project_id' => $project->id
        ));
        $criteria->order = 'name ASC, email ASC';

        if (count($excludeIds)) {
            $criteria->addNotInCondition('user_id', $excludeIds);
        }

        $users = ProjectUser::model()->with('user')->findAll($criteria);

		// display the page
        $this->pageTitle = $checkName;
		$this->render('edit', array(
            'model' => $model,
            'project' => $project,
            'admins' => $admins,
            'users' => $users,
            'statuses' => array(
                TargetCheck::STATUS_VULN_OPEN => Yii::t('app', 'Open'),
                TargetCheck::STATUS_VULN_RESOLVED => Yii::t('app', 'Resolved'),
            )
        ));
	}
}
