<?php

/**
 * Account controller.
 */
class AccountController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
            'https',
			'checkAuth + edit, certificate',
		);
	}

    /**
     * Edit account
     */
	public function actionEdit()
	{
        $model = new AccountEditForm();
        $user  = User::model()->findByPk(Yii::app()->user->id);

        $model->email = $user->email;
        $model->name = $user->name;
        $model->sendNotifications = $user->send_notifications;
        $model->certificateRequired = $user->certificate_required;

        // collect user input data
		if (isset($_POST['AccountEditForm']))
		{
			$model->attributes = $_POST['AccountEditForm'];

            if (Yii::app()->user->role == User::ROLE_CLIENT || !isset($_POST['AccountEditForm']['sendNotifications']))
                $model->sendNotifications = false;

            if (!isset($_POST['AccountEditForm']['certificateRequired'])) {
                $model->certificateRequired = false;
            }

			if ($model->validate())
            {
                $checkEmail = User::model()->findByAttributes(array( 'email' => $model->email ));

                if (!$checkEmail || $checkEmail->id == $user->id)
                {
                    $user->email = $model->email;
                    $user->name = $model->name;
                    $user->send_notifications = $model->sendNotifications;
                    $user->certificate_required = $model->certificateRequired;

                    if ($model->password)
                        $user->password = hash('sha256', $model->password);

                    $user->save();

                    Yii::app()->user->setFlash('success', Yii::t('app', 'Account saved.'));
                }
                else
                    $model->addError('email', Yii::t('app', 'User with this e-mail address already exists.'));
            }

            if (count($model->getErrors()) > 0)
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Account'), '');

		// display the page
        $this->pageTitle = Yii::t('app', 'Account');
		$this->render('edit', array(
            'model' => $model,
            'user'  => $user
        ));
    }

    /**
     * Restore account password
     */
	public function actionRestore()
	{
        if (!Yii::app()->user->isGuest) {
            $this->redirect(array('project/index'));
        }

        $model = new AccountRestoreForm(AccountRestoreForm::REQUEST_CODE_SCENARIO);
        $success = false;

        // collect user input data
		if (isset($_POST['AccountRestoreForm'])) {
			$model->attributes = $_POST['AccountRestoreForm'];

			if ($model->validate()) {
                $user = User::model()->findByAttributes(array('email' => $model->email));
                $user->password_reset_code = hash('sha256', $user->email . time() . rand());
                $user->password_reset_time = new CDbExpression("NOW()");
                $user->save();

                $email = new Email();
                $email->user_id = $user->id;
                $email->subject = Yii::t('app', 'Restore Account');

                $email->content = $this->renderPartial(
                    'application.views.email.account_restore',

                    array(
                        'userName' => $user->name ? CHtml::encode($user->name) : $user->email,
                        'url' => $this->createUrl("account/changepassword", array("code" => $user->password_reset_code))
                    ),

                    true
                );

                $email->save();
                $success = true;
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

		// display the page
        $this->pageTitle = Yii::t('app', 'Restore Account');
		$this->render('restore', array(
            'model' => $model,
            'success' => $success
        ));
    }

    /**
     * Change account password
     */
	public function actionChangePassword($code)
	{
        if (!Yii::app()->user->isGuest) {
            $this->redirect(array('project/index'));
        }

        $model = new AccountRestoreForm(AccountRestoreForm::RESET_PASSWORD_SCENARIO);
        $user = User::model()->find(array(
            "condition" => "password_reset_code = :code AND password_reset_time + INTERVAL '10 MINUTES' >= NOW()",
            "params" => array("code" => $code)
        ));

        if (!$user) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        // collect user input data
		if (isset($_POST['AccountRestoreForm'])) {
			$model->attributes = $_POST['AccountRestoreForm'];

			if ($model->validate()) {
                $user->password = hash('sha256', $model->password);
                $user->password_reset_code = null;
                $user->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Password changed.'));
                $this->redirect(array('app/login'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

		// display the page
        $this->pageTitle = Yii::t('app', 'Restore Account');
		$this->render('changepassword', array(
            'model' => $model,
            'user'  => $user
        ));
    }

    /**
     * Generate certificate
     */
    public function actionCertificate() {
        Certificate::generate(Yii::app()->user->id);
    }
}
