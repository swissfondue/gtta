<?php

/**
 * Client controller.
 */
class ClientController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
            'https',
			'checkAuth',
            'checkUser',
            'checkAdmin + edit, control',
            'ajaxOnly + controllogo',
            'postOnly + uploadlogo, controllogo',
            "idleOrRunning",
		);
	}

    /**
     * Display a list of clients.
     */
	public function actionIndex($page=1)
	{
        $page = (int) $page;

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.name ASC';

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

        $clientCount = Client::model()->count($criteria);
        $paginator   = new Paginator($clientCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Clients'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Clients');
		$this->render('index', array(
            'clients' => $clients,
            'p'       => $paginator
        ));
	}

    /**
     * Display a list of projects for the given client.
     */
	public function actionView($id, $page=1)
	{
        $id   = (int) $id;
        $page = (int) $page;

        $client = Client::model()->findByPk($id);

        if (!$client)
            throw new CHttpException(404, Yii::t('app', 'Client not found.'));

        if (!$client->checkPermission())
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.deadline ASC, t.name ASC';
        $criteria->addColumnCondition(array(
            't.client_id' => $client->id
        ));
        $criteria->together = true;

        if (User::checkRole(User::ROLE_ADMIN))
        {
            $projects     = Project::model()->findAll($criteria);
            $projectCount = Project::model()->count($criteria);
        }
        else
        {
            $projects = Project::model()->with(array(
                'project_users' => array(
                    'joinType' => 'INNER JOIN',
                    'on'       => 'project_users.user_id = :user_id',
                    'params'   => array(
                        'user_id' => Yii::app()->user->id,
                    ),
                ),
            ))->findAll($criteria);

            $projectCount = Project::model()->with(array(
                'project_users' => array(
                    'joinType' => 'INNER JOIN',
                    'on'       => 'project_users.user_id = :user_id',
                    'params'   => array(
                        'user_id' => Yii::app()->user->id,
                    ),
                ),
            ))->count($criteria);
        }

        $paginator = new Paginator($projectCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Clients'), $this->createUrl('client/index'));
        $this->breadcrumbs[] = array($client->name, '');

        // display the page
        $this->pageTitle = $client->name;
		$this->render('view', array(
            'client'   => $client,
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
     * Client edit page.
     */
	public function actionEdit($id=0)
	{
        $id        = (int) $id;
        $newRecord = false;

        if ($id)
            $client = Client::model()->findByPk($id);
        else
        {
            $client    = new Client();
            $newRecord = true;
        }

		$model = new ClientEditForm();

        if (!$newRecord)
        {
            $model->name         = $client->name;
            $model->country      = $client->country;
            $model->state        = $client->state;
            $model->city         = $client->city;
            $model->address      = $client->address;
            $model->postcode     = $client->postcode;
            $model->website      = $client->website;
            $model->contactEmail = $client->contact_email;
            $model->contactName  = $client->contact_name;
            $model->contactPhone = $client->contact_phone;
            $model->contactFax   = $client->contact_fax;
        }

		// collect user input data
		if (isset($_POST['ClientEditForm']))
		{
			$model->attributes = $_POST['ClientEditForm'];

			if ($model->validate())
            {                
                $client->name          = $model->name;
                $client->country       = $model->country;
                $client->state         = $model->state;
                $client->city          = $model->city;
                $client->address       = $model->address;
                $client->postcode      = $model->postcode;
                $client->website       = $model->website;
                $client->contact_email = $model->contactEmail;
                $client->contact_name  = $model->contactName;
                $client->contact_phone = $model->contactPhone;
                $client->contact_fax   = $model->contactFax;

                $client->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Client saved.'));

                $client->refresh();

                if ($newRecord)
                    $this->redirect(array( 'client/edit', 'id' => $client->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Clients'), $this->createUrl('client/index'));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Client'), '');
        else
        {
            $this->breadcrumbs[] = array($client->name, $this->createUrl('client/view', array( 'id' => $client->id )));
            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Client') : $client->name;
		$this->render('edit', array(
            'model'  => $model,
            'client' => $client,
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

            $id     = $model->id;
            $client = Client::model()->findByPk($id);

            if ($client === null)
                throw new CHttpException(404, Yii::t('app', 'Client not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $client->delete();
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
     * Search action.
     */
    public function actionSearch()
    {
        $model   = new SearchForm();
        $clients = array();

        if (isset($_POST['SearchForm']))
        {
            $model->attributes = $_POST['SearchForm'];

            if ($model->validate())
            {
                $criteria = new CDbCriteria();
                $criteria->order = 't.name ASC';
                $criteria->together = true;

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

                $searchCriteria = new CDbCriteria();
                $searchCriteria->addSearchCondition('t.name', $model->query, true, 'OR', 'ILIKE');
                $searchCriteria->addSearchCondition('t.country', $model->query, true, 'OR', 'ILIKE');
                $searchCriteria->addSearchCondition('t.state', $model->query, true, 'OR', 'ILIKE');
                $searchCriteria->addSearchCondition('t.city', $model->query, true, 'OR', 'ILIKE');
                $searchCriteria->addSearchCondition('t.website', $model->query, true, 'OR', 'ILIKE');
                $criteria->mergeWith($searchCriteria);

                $clients = Client::model()->findAll($criteria);
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Clients'), $this->createUrl('client/index'));
        $this->breadcrumbs[] = array(Yii::t('app', 'Search'), '');

		// display the page
        $this->pageTitle = Yii::t('app', 'Search');
		$this->render('search', array(
            'model'   => $model,
            'clients' => $clients,
        ));
    }

    /**
     * Upload logo.
     */
    function actionUploadLogo($id)
    {
        $response = new AjaxResponse();

        try
        {
            $id = (int) $id;

            $client = Client::model()->findByPk($id);

            if (!$client)
                throw new CHttpException(404, Yii::t('app', 'Client not found.'));

            $model = new ClientLogoUploadForm();
            $model->image = CUploadedFile::getInstanceByName('ClientLogoUploadForm[image]');

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

            // delete the old image
            if ($client->logo_path)
                @unlink(Yii::app()->params['clientLogos']['path'] . '/' . $client->logo_path);

            $client->logo_type = $model->image->type;
            $client->logo_path = hash('sha256', $model->image->name . rand() . time());
            $client->save();

            $model->image->saveAs(Yii::app()->params['clientLogos']['path'] . '/' . $client->logo_path);

            $response->addData('url', $this->createUrl('client/logo', array( 'id' => $client->id )));
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control logo.
     */
    public function actionControlLogo()
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

            $client = Client::model()->findByPk($model->id);

            if ($client === null)
                throw new CHttpException(404, Yii::t('app', 'Client not found.'));

            if (!$client->logo_path)
                throw new CHttpException(404, Yii::t('app', 'Logo not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    @unlink(Yii::app()->params['clientLogos']['path'] . '/' . $client->logo_path);
                    $client->logo_path = NULL;
                    $client->logo_type = NULL;
                    $client->save();

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
     * Get logo.
     */
    public function actionLogo($id)
    {
        $id = (int) $id;

        $client = Client::model()->findByPk($id);

        if ($client === null)
            throw new CHttpException(404, Yii::t('app', 'Client not found.'));

        if (!$client->logo_path)
            throw new CHttpException(404, Yii::t('app', 'Logo not found.'));

        $filePath = Yii::app()->params['clientLogos']['path'] . '/' . $client->logo_path;

        if (!file_exists($filePath))
            throw new CHttpException(404, Yii::t('app', 'Logo not found.'));

        $extension = 'jpg';

        if ($client->logo_type == 'image/png')
            $extension = 'png';

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $client->logo_type);
        header('Content-Disposition: attachment; filename="logo.' . $extension . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }
}
