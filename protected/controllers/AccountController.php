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
			'checkAuth',
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

        // collect user input data
		if (isset($_POST['AccountEditForm']))
		{
			$model->attributes = $_POST['AccountEditForm'];

            if (Yii::app()->user->role == User::ROLE_CLIENT || !isset($_POST['AccountEditForm']['sendNotifications']))
                $model->sendNotifications = false;

			if ($model->validate())
            {
                $checkEmail = User::model()->findByAttributes(array( 'email' => $model->email ));

                if (!$checkEmail || $checkEmail->id == $user->id)
                {
                    $user->email = $model->email;
                    $user->name = $model->name;
                    $user->send_notifications = $model->sendNotifications;

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
}
