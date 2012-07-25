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
			'checkAuth',
            'checkUser',
            'checkAdmin + control',
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

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.deadline ASC, t.name ASC';
        $criteria->addCondition('t.client_id = :client_id');
        $criteria->params = array( 'client_id' => $client->id );

        $projects = Project::model()->findAll($criteria);

        $projectCount = Project::model()->count($criteria);
        $paginator    = new Paginator($projectCount, $page);

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
}
