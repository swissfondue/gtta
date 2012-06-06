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
			'checkAuth',
            'checkAdmin',
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
        $criteria->order  = 't.role ASC, t.email ASC';

        $users = User::model()->findAll($criteria);

        $userCount = User::model()->count($criteria);
        $paginator = new Paginator($userCount, $page);

        $this->breadcrumbs[Yii::t('app', 'Users')] = '';

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
            $user     = new User();
            $newRecord = true;
        }

		$model = new UserEditForm($id ? UserEditForm::EDIT_USER_SCENARIO : UserEditForm::ADD_USER_SCENARIO);

        if (!$newRecord)
        {
            $model->email    = $user->email;
            $model->name     = $user->name;
            $model->role     = $user->role;
            $model->clientId = $user->client_id;
        }

		// collect user input data
		if (isset($_POST['UserEditForm']))
		{
			$model->attributes = $_POST['UserEditForm'];

			if ($model->validate())
            {
                $checkEmail = User::model()->findByAttributes(array( 'email' => $model->email ));

                if (!$checkEmail || $checkEmail->id == $user->id)
                {
                    $user->email     = $model->email;
                    $user->name      = $model->name;
                    $user->role      = $model->role;

                    if ($user->role == User::ROLE_CLIENT && $model->clientId)
                        $user->client_id = $model->clientId;
                    else
                        $user->client_id = NULL;

                    if ($model->password)
                        $user->password = hash('sha256', $model->password);

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

        $this->breadcrumbs[Yii::t('app', 'Users')]  = $this->createUrl('user/index');

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New User')] = '';
        else
            $this->breadcrumbs[$user->email] = '';

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New User') : $user->email;
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
}