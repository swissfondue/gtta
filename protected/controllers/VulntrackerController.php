<?php

/**
 * Vulnerability tracker controller.
 */
class VulntrackerController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
            'https',
			'checkAuth',
        );
	}

    /**
     * Display a list of projects.
     */
	public function actionIndex()
	{
        $model = new ProjectSelectForm();

        if (isset($_POST['ProjectSelectForm']))
        {
            $model->attributes = $_POST['ProjectSelectForm'];

            if ($model->validate())
            {
                $this->redirect(array( 'vulntracker/vulns', 'id' => $model->projectId ));
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
        $criteria->order = 'target.host ASC, COALESCE(l10n.name, "check".name) ASC';
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

        Yii::app()->user->returnUrl = $this->createUrl('vulntracker/vulns', array( 'id' => $project->id, 'page' => $page ));

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Tracker'), $this->createUrl('vulntracker/index'));
        $this->breadcrumbs[] = array($project->name, '');

        // display the page
        $this->pageTitle = $project->name;
		$this->render('vulns', array(
            'project'      => $project,
            'checks'       => $checks,
            'p'            => $paginator,
            'ratings' => array(
                TargetCheck::RATING_LOW_RISK  => Yii::t('app', 'Low Risk'),
                TargetCheck::RATING_MED_RISK  => Yii::t('app', 'Med Risk'),
                TargetCheck::RATING_HIGH_RISK => Yii::t('app', 'High Risk'),
            ),
            'statuses' => array(
                TargetCheckVuln::STATUS_OPEN     => Yii::t('app', 'Open'),
                TargetCheckVuln::STATUS_RESOLVED => Yii::t('app', 'Resolved'),
            ),
        ));
    }

    /**
     * Vulnerability edit page.
     */
	public function actionEdit($id, $target, $check)
	{
        $id        = (int) $id;
        $target    = (int) $target;
        $check     = (int) $check;
        $newRecord = false;

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

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

        if (!$check || !in_array($check->rating, array( TargetCheck::RATING_LOW_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_HIGH_RISK )))
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));

        $vuln = TargetCheckVuln::model()->findByAttributes(array(
            'check_id'  => $check->check_id,
            'target_id' => $check->target_id
        ));

        if (!$vuln)
        {
            $vuln = new TargetCheckVuln();
            $vuln->check_id  = $check->check_id;
            $vuln->target_id = $check->target_id;
            $newRecord = true;
        }

		$model = new VulnEditForm();

        if (!$newRecord)
        {
            $model->status   = $vuln->status;
            $model->userId   = $vuln->user_id;
            $model->deadline = $vuln->deadline;
        }
        else
            $model->deadline = date('Y-m-d');

		// collect user input data
		if (isset($_POST['VulnEditForm']))
		{
			$model->attributes = $_POST['VulnEditForm'];

            if (!$model->userId)
                $model->userId = null;

			if ($model->validate())
            {
                $vuln->status   = $model->status;
                $vuln->user_id  = $model->userId;
                $vuln->deadline = $model->deadline;

                $vuln->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Vulnerability saved.'));

                $project->refresh();

                $this->redirect(Yii::app()->user->returnUrl);
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Tracker'), $this->createUrl('vulntracker/index'));
        $this->breadcrumbs[] = array($project->name, $this->createUrl('vulntracker/vulns', array( 'id' => $project->id )));
        $this->breadcrumbs[] = array($check->check->localizedName, '');

        $admins = User::model()->findAllByAttributes(array(
            'role' => User::ROLE_ADMIN
        ));

        $excludeIds = array();

        foreach ($admins as $admin)
            $excludeIds[] = $admin->id;

        $clients = User::model()->findAllByAttributes(array(
            'role'      => User::ROLE_CLIENT,
            'client_id' => $project->client_id
        ));

        foreach ($clients as $client)
            $excludeIds[] = $client->id;

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            'project_id' => $project->id
        ));
        $criteria->order = 'name ASC, email ASC';

        if (count($excludeIds))
            $criteria->addNotInCondition('user_id', $excludeIds);

        $users = ProjectUser::model()->with('user')->findAll($criteria);

		// display the page
        $this->pageTitle = $check->check->localizedName;
		$this->render('edit', array(
            'model'    => $model,
            'project'  => $project,
            'admins'   => $admins,
            'users'    => $users,
            'statuses' => array(
                TargetCheckVuln::STATUS_OPEN     => Yii::t('app', 'Open'),
                TargetCheckVuln::STATUS_RESOLVED => Yii::t('app', 'Resolved'),
            )
        ));
	}
}
