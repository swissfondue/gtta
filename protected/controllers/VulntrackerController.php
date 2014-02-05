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
            "idleOrRunning",
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
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->together = true;

        $checks = TargetCheck::model()->with(array(
            'check' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on' => 'l10n.language_id = :language_id',
                        'params' => array( 'language_id' => $language )
                    ),
                ),
            ),
            'vuln' => array(
                'with' => 'user'
            ),
            'target',
        ))->findAll($criteria);

        $checkCount = TargetCheck::model()->count($criteria);

        return array($checks, $checkCount);
    }

    /**
     * GT project vulnerabilities
     * @param $project
     * @param $page
     */
    private function _gtProjectVulns($project, $language, $page) {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.rating', $this->_allowedRiskValues);
        $criteria->order = 't.target ASC, COALESCE(l10n.name, "check".name) ASC';
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->together = true;

        $checks = ProjectGtCheck::model()->with(array(
            'check' => array(
                'with' => array(
                    'check' => array(
                        'alias' => 'innerCheck',
                        'with' => array(
                            'l10n' => array(
                                'joinType' => 'LEFT JOIN',
                                'on' => 'l10n.language_id = :language_id',
                                'params' => array('language_id' => $language)
                            ),
                        )
                    )
                ),
            ),
            'vuln' => array(
                'with' => 'user'
            ),
        ))->findAll($criteria);

        $checkCount = ProjectGtCheck::model()->count($criteria);

        return array($checks, $checkCount);
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

        if ($project->guided_test) {
            list($checks, $checkCount) = $this->_gtProjectVulns($project, $language, $page);
        } else {
            list($checks, $checkCount) = $this->_projectVulns($project, $language, $page);
        }

        $paginator = new Paginator($checkCount, $page);
        Yii::app()->user->returnUrl = $this->createUrl('vulntracker/vulns', array('id' => $project->id, 'page' => $page));

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Tracker'), $this->createUrl('vulntracker/index'));
        $this->breadcrumbs[] = array($project->name, '');

        // display the page
        $this->pageTitle = $project->name;
		$this->render('vulns', array(
            'project' => $project,
            'checks' => $checks,
            'p' => $paginator,
            'ratings' => TargetCheck::getRatingNames(),
            'statuses' => array(
                TargetCheckVuln::STATUS_OPEN => Yii::t('app', 'Open'),
                TargetCheckVuln::STATUS_RESOLVED => Yii::t('app', 'Resolved'),
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
    private function _projectEdit($project, $language, $target, $check) {
        $newRecord = false;

        $check = TargetCheck::model()->with(array(
            'check' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    ),
                ),
            )
        ))->findByAttributes(array(
            'check_id'  => $check,
            'target_id' => $target
        ));

        if (!$check || !in_array($check->rating, $this->_allowedRiskValues)) {
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));
        }

        $vuln = TargetCheckVuln::model()->findByAttributes(array(
            'check_id'  => $check->check_id,
            'target_id' => $check->target_id
        ));

        if (!$vuln) {
            $vuln = new TargetCheckVuln();
            $vuln->check_id  = $check->check_id;
            $vuln->target_id = $check->target_id;
            $newRecord = true;
        }

		$model = new VulnEditForm();

        if (!$newRecord) {
            $model->status = $vuln->status;
            $model->userId = $vuln->user_id;
            $model->deadline = $vuln->deadline;
        } else {
            $model->deadline = date('Y-m-d');
        }

		// collect user input data
		if (isset($_POST['VulnEditForm'])) {
			$model->attributes = $_POST['VulnEditForm'];

            if (!$model->userId) {
                $model->userId = null;
            }

			if ($model->validate()) {
                $vuln->status = $model->status;
                $vuln->user_id = $model->userId;
                $vuln->deadline = $model->deadline;

                $vuln->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Vulnerability saved.'));

                $project->refresh();

                $this->redirect(Yii::app()->user->returnUrl);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        return array($check->check->localizedName, $model);
    }

    /**
     * GT project edit
     * @param $project
     * @param $language
     * @param $check
     */
    private function _gtProjectEdit($project, $language, $check) {
        $newRecord = false;

        $check = ProjectGtCheck::model()->with(array(
            'check' => array(
                'with' => array(
                    'check' => array(
                        'alias' => 'innerCheck',
                        'with' => array(
                            'l10n' => array(
                                'joinType' => 'LEFT JOIN',
                                'on' => 'l10n.language_id = :language_id',
                                'params' => array('language_id' => $language)
                            ),
                        )
                    )
                ),
            )
        ))->findByAttributes(array(
            'gt_check_id' => $check,
            'project_id' => $project->id
        ));

        if (!$check || !in_array($check->rating, $this->_allowedRiskValues)) {
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));
        }

        $vuln = ProjectGtCheckVuln::model()->findByAttributes(array(
            'gt_check_id' => $check->gt_check_id,
            'project_id' => $project->id
        ));

        if (!$vuln) {
            $vuln = new ProjectGtCheckVuln();
            $vuln->gt_check_id  = $check->gt_check_id;
            $vuln->project_id = $project->id;
            $newRecord = true;
        }

		$model = new VulnEditForm();

        if (!$newRecord) {
            $model->status = $vuln->status;
            $model->userId = $vuln->user_id;
            $model->deadline = $vuln->deadline;
        } else {
            $model->deadline = date('Y-m-d');
        }

		// collect user input data
		if (isset($_POST['VulnEditForm'])) {
			$model->attributes = $_POST['VulnEditForm'];

            if (!$model->userId) {
                $model->userId = null;
            }

			if ($model->validate()) {
                $vuln->status = $model->status;
                $vuln->user_id = $model->userId;
                $vuln->deadline = $model->deadline;

                $vuln->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Vulnerability saved.'));

                $project->refresh();

                $this->redirect(Yii::app()->user->returnUrl);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        return array($check->check->check->localizedName, $model);
    }

    /**
     * Vulnerability edit page.
     */
	public function actionEdit($id, $target, $check) {
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

        if ($project->guided_test) {
            list($checkName, $model) = $this->_gtProjectEdit($project, $language, $check);
        } else {
            list($checkName, $model) = $this->_projectEdit($project, $language, $target, $check);
        }

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
                TargetCheckVuln::STATUS_OPEN => Yii::t('app', 'Open'),
                TargetCheckVuln::STATUS_RESOLVED => Yii::t('app', 'Resolved'),
            )
        ));
	}
}
