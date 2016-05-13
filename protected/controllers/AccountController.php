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
			'checkAuth  + edit, certificate, time, controltimerecord',
            'ajaxOnly   + controltimerecord',
            'postOnly   + controltimerecord'
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
                $now = new DateTime();
                $user->password_reset_time = DateTimeFormat::toISO($now->getTimestamp());
                $user->save();

                $subject = Yii::t('app', 'Restore Account');
                $content = $this->renderPartial(
                    'application.views.email.account_restore',
                    array(
                        'userName' => $user->name ? CHtml::encode($user->name) : $user->email,
                        'url' => $this->createUrl("account/changepassword", array("code" => $user->password_reset_code))
                    ),
                    true
                );

                EmailJob::enqueue(array(
                    "user_id" => $user->id,
                    "subject" => $subject,
                    "content" => $content,
                ));

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
        $now = new DateTime();

        $user = User::model()->find(array(
            "condition" => "password_reset_code = :code AND password_reset_time + INTERVAL '10 MINUTES' >= :time",
            "params" => array("code" => $code, "time" => $now->format(ISO_DATE_TIME))
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
        OpenSSL::generateUserCertificate(Yii::app()->user->id);
    }

    /**
     * Display user's time records list
     */
    public function actionTime($limited=false, $page=1) {
        $page = (int) $page;

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            "user_id" => Yii::app()->user->id
        ));
        $criteria->addCondition("time IS NOT NULL");
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->order = "create_time DESC";
        $criteria->together = true;

        $unformatted = ProjectTime::model()->findAll($criteria);
        $records = array();

        foreach ($unformatted as $r) {
            $records[] = $r->formatted;
        }

        $recordCount = ProjectTime::model()->count($criteria);

        $this->breadcrumbs[] = array(Yii::t("app", "Time Tracker"), '');
        $this->pageTitle = Yii::t("app", "Time Tracker");

        $paginator = new Paginator($recordCount, $page);

        $this->render("time", array(
            "records" => $records,
            "p" => $paginator,
        ));
    }

    /**
     * Control time record
     */
    public function actionControlTimeRecord() {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $user = User::model()->findByPk(Yii::app()->user->id);

            switch ($model->operation) {
                case "start":
                    $project = Project::model()->findByPk($model->id);

                    if (!$project) {
                        throw new CHttpException(404, Yii::t("app", "Project not found."));
                    }

                    if ($user->timeSession) {
                        $user->timeSession->stop();
                    }

                    $now = new DateTime();
                    $record = new ProjectTime();
                    $record->create_time = $now->format(ISO_DATE_TIME);
                    $record->start_time = $now->format(ISO_DATE_TIME);
                    $record->project_id = $project->id;
                    $record->user_id = $user->id;
                    $record->description = Yii::t("app", "Project time tracker.");
                    $record->save();

                    break;

                case "stop":
                    $project = Project::model()->findByPk($model->id);

                    if (!$project) {
                        throw new CHttpException(404, Yii::t("app", "Project not found."));
                    }

                    /** @var ProjectTime $record */
                    $record = ProjectTime::model()->findByAttributes(array(
                        "user_id" => $user->id,
                        "project_id" => $project->id,
                        "time" => null,
                    ));

                    if (!$record) {
                        throw new Exception(Yii::t("app", "Time session not started."));
                    }

                    $record->stop();

                    break;

                case "delete":
                    $record = ProjectTime::model()->findByPk($model->id);

                    if (!$record){
                        throw new Exception("Time record not found.");
                    }

                    $record->delete();

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}
