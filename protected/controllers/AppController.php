<?php

/**
 * Main app controller.
 */
class AppController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
            'https',
			'checkAuth - login, error, maintenance, l10n',
		);
	}

    /**
     * If user is logged in then redirect to a project list, otherwise
     * redirect to a login form.
     */
	public function actionIndex()
	{
        $this->redirect(array( 'project/index' ));
	}

    /**
     * Log the user in and redirect to a project list
     */
	public function actionLogin()
	{
        if (!Yii::app()->user->isGuest)
            $this->redirect(array( 'project/index' ));

		$model = new LoginForm();

		// collect user input data
		if (isset($_POST['LoginForm']))
		{
			$model->attributes = $_POST['LoginForm'];

			if ($model->validate())
            {
                if ($model->login())
				    $this->redirect(Yii::app()->user->returnUrl);
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

		// display the login form
        $this->pageTitle = Yii::t('app', 'Login');
		$this->render('login', array(
            'model' => $model
        ));
	}

    /**
     * Log the user out and redirect to the main page
     */
	public function actionLogout()
	{
        Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Exception handler
	 */
	public function actionError()
	{
	    $error = Yii::app()->errorHandler->error;
        $this->breadcrumbs[] = array(Yii::t('app', 'Error'), '');

        if ($error)
	    {
	    	if (Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
            {
                $this->pageTitle = Yii::t('app', 'Error {code}', array( '{code}' => $error['code'] ));
	        	$this->render('error', array( 'message' => $error['message'] ));
            }
	    }
	}

    /**
	 * Maintenance handler
	 */
	public function actionMaintenance()
	{
        $this->breadcrumbs[] = array(Yii::t('app', 'Maintenance'), '');
        $this->pageTitle = Yii::t('app', 'Maintenance');
        $this->render('maintenance');
	}

    /**
     * Localization javascript file.
     */
    public function actionL10n()
    {
        header('Content-Type: text/javascript');
        echo $this->renderPartial('l10n');
    }
}
