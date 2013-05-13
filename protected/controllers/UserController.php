<?php

/**
 * User controller.
 */
class UserController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
            'https',
			'checkAuth',
            'checkAdmin',
            'ajaxOnly + control, controlproject, objectlist',
            'postOnly + control, controlproject, objectlist',
		);
	}

    /**
     * Display a list of users.
     */
	public function actionIndex($page=1)
	{
        $page = (int) $page;

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.role ASC, t.name ASC, t.email ASC';

        $users = User::model()->findAll($criteria);

        $userCount = User::model()->count($criteria);
        $paginator = new Paginator($userCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Users'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Users');
		$this->render('index', array(
            'users' => $users,
            'p'     => $paginator,
            'roles' => array(
                User::ROLE_ADMIN  => Yii::t('app', 'Admin'),
                User::ROLE_USER   => Yii::t('app', 'User'),
                User::ROLE_CLIENT => Yii::t('app', 'Client'),
            ),
        ));
	}

    /**
     * User edit page.
     */
	public function actionEdit($id=0)
	{
        $id        = (int) $id;
        $newRecord = false;

        if ($id)
            $user = User::model()->findByPk($id);
        else
        {
            $user      = new User();
            $newRecord = true;
        }

		$model = new UserEditForm($id ? UserEditForm::EDIT_USER_SCENARIO : UserEditForm::ADD_USER_SCENARIO);

        if (!$newRecord) {
            $model->email = $user->email;
            $model->name = $user->name;
            $model->role = $user->role;
            $model->clientId = $user->client_id;
            $model->sendNotifications = $user->send_notifications;
            $model->showReports = $user->show_reports;
            $model->showDetails = $user->show_details;
        }

		// collect user input data
		if (isset($_POST['UserEditForm']))
		{
			$model->attributes = $_POST['UserEditForm'];

			if ($model->validate())
            {
                $checkEmail = User::model()->findByAttributes(array( 'email' => $model->email ));

                if ($user->role == User::ROLE_CLIENT || !isset($_POST['UserEditForm']['sendNotifications']))
                    $model->sendNotifications = false;

                if (!isset($_POST['UserEditForm']['showDetails'])) {
                    $model->showDetails = false;
                }

                if (!isset($_POST['UserEditForm']['showReports'])) {
                    $model->showReports = false;
                }

                if (!$checkEmail || $checkEmail->id == $user->id)
                {
                    $user->email = $model->email;
                    $user->name  = $model->name;
                    $user->role  = $model->role;
                    $user->send_notifications = $model->sendNotifications;

                    // delete all projects from this client account
                    if (!$newRecord && $user->role == User::ROLE_CLIENT && $model->clientId != $user->client_id)
                        ProjectUser::model()->deleteAllByAttributes(array(
                            'user_id' => $user->id
                        ));

                    if ($user->role == User::ROLE_CLIENT && $model->clientId)
                        $user->client_id = $model->clientId;
                    else
                        $user->client_id = NULL;

                    if ($model->password)
                        $user->password = hash('sha256', $model->password);

                    if ($user->role == User::ROLE_CLIENT) {
                        $user->show_details = $model->showDetails;
                        $user->show_reports = $model->showReports;
                    } else {
                        $user->show_details = false;
                        $user->show_reports = false;
                    }

                    $user->save();

                    Yii::app()->user->setFlash('success', Yii::t('app', 'User saved.'));

                    $user->refresh();

                    if ($newRecord)
                        $this->redirect(array( 'user/edit', 'id' => $user->id ));
                }
                else
                    $model->addError('email', Yii::t('app', 'User with this e-mail address already exists.'));
            }

            if (count($model->getErrors()) > 0)
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Users'), $this->createUrl('user/index'));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New User'), '');
        else
            $this->breadcrumbs[] = array($user->name ? $user->name : $user->email, '');

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New User') : $user->name ? $user->name : $user->email;
		$this->render('edit', array(
            'model'   => $model,
            'user'    => $user,
            'clients' => $clients,
            'roles'   => array(
                User::ROLE_ADMIN  => Yii::t('app', 'Admin'),
                User::ROLE_USER   => Yii::t('app', 'User'),
                User::ROLE_CLIENT => Yii::t('app', 'Client'),
            ),
        ));
	}

    /**
     * Control function.
     */
    public function actionControl()
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

            $id   = $model->id;
            $user = User::model()->findByPk($id);

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
     * Display a list of projects.
     */
	public function actionProjects($id, $page=1)
	{
        $id   = (int) $id;
        $page = (int) $page;

        $user = User::model()->findByPk($id);

        if ($user === null)
            throw new CHttpException(404, Yii::t('app', 'User not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 'deadline ASC, project.name ASC';
        $criteria->addColumnCondition(array(
            'user_id' => $user->id
        ));

        $projects = ProjectUser::model()->with(array(
            'project' => array(
                'with' => 'client'
            )
        ))->findAll($criteria);

        $projectCount = ProjectUser::model()->count($criteria);
        $paginator    = new Paginator($projectCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Users'), $this->createUrl('user/index'));
        $this->breadcrumbs[] = array($user->name ? $user->name : $user->email, $this->createUrl('user/edit', array( 'id' => $user->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Projects');
		$this->render('project/index', array(
            'user'     => $user,
            'projects' => $projects,
            'p'        => $paginator,
            'statuses' => array(
                Project::STATUS_OPEN        => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED    => Yii::t('app', 'Finished'),
            )
        ));
	}

    /**
     * Project add page.
     */
	public function actionAddProject($id)
	{
        $id   = (int) $id;
        $user = User::model()->findByPk($id);

        if (!$user)
            throw new CHttpException(404, Yii::t('app', 'User not found.'));

		$model = new UserProjectAddForm();

		// collect user input data
		if (isset($_POST['UserProjectAddForm']))
		{
			$model->attributes = $_POST['UserProjectAddForm'];

			if ($model->validate())
            {
                $check = ProjectUser::model()->findByAttributes(array(
                    'project_id' => $model->projectId,
                    'user_id'    => $user->id
                ));

                if (!$check)
                {
                    $project = Project::model()->findByPk($model->projectId);

                    if ($user->role == User::ROLE_CLIENT && $user->client_id != $project->client_id)
                        Yii::app()->user->setFlash('error', Yii::t('app', 'Project belongs to another client.'));
                    else
                    {
                        $project = new ProjectUser();
                        $project->project_id = $model->projectId;
                        $project->user_id = $user->id;
                        $project->admin = 0;
                        $project->save();

                        Yii::app()->user->setFlash('success', Yii::t('app', 'Project added.'));
                    }
                }
                else
                    Yii::app()->user->setFlash('error', Yii::t('app', 'Project is already added for this user.'));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        if ($user->role == User::ROLE_CLIENT)
            $clients = Client::model()->findAllByAttributes(array(
                'id' => $user->client_id
            ));
        else
            $clients = Client::model()->findAllByAttributes(
                array(),
                array( 'order' => 't.name ASC' )
            );

        $this->breadcrumbs[] = array(Yii::t('app', 'Users'), $this->createUrl('user/index'));
        $this->breadcrumbs[] = array($user->name ? $user->name : $user->email, $this->createUrl('user/edit', array( 'id' => $user->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Projects'), $this->createUrl('user/projects', array( 'id' => $user->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Add Project'), '');

		// display the page
        $this->pageTitle = Yii::t('app', 'Add Project');
		$this->render('project/add', array(
            'model'   => $model,
            'user'    => $user,
            'clients' => $clients,
        ));
	}

    /**
     * Control project function.
     */
    public function actionControlProject($id)
    {
        $response = new AjaxResponse();

        try
        {
            $id   = (int) $id;
            $user = User::model()->findByPk($id);

            if (!$user)
                throw new CHttpException(404, Yii::t('app', 'User not found.'));

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

            $project = ProjectUser::model()->findByAttributes(array(
                'project_id' => $model->id,
                'user_id'    => $user->id
            ));

            if ($project === null)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $project->delete();
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
     * Object list.
     */
    public function actionObjectList($id)
    {
        $response = new AjaxResponse();

        try
        {
            $id   = (int) $id;
            $user = User::model()->findByPk($id);

            if (!$user)
                throw new CHttpException(404, Yii::t('app', 'User not found.'));

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

            $objects = array();

            switch ($model->operation)
            {
                case 'project-list':
                    $client = Client::model()->findByPk($model->id);

                    if (!$client)
                        throw new CHttpException(404, Yii::t('app', 'Client not found.'));

                    $userProjects = ProjectUser::model()->findAllByAttributes(array(
                        'user_id' => $user->id
                    ));

                    $userProjectIds = array();

                    foreach ($userProjects as $project)
                        $userProjectIds[] = $project->project_id;

                    $criteria = new CDbCriteria();
                    $criteria->addNotInCondition('id', $userProjectIds);
                    $criteria->addColumnCondition(array(
                        'client_id' => $client->id
                    ));
                    $criteria->order = 't.name ASC, t.year ASC';

                    $projects = Project::model()->findAll($criteria);

                    foreach ($projects as $project)
                        $objects[] = array(
                            'id'   => $project->id,
                            'name' => CHtml::encode($project->name) . ' (' . $project->year . ')',
                        );

                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }

            $response->addData('objects', $objects);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}