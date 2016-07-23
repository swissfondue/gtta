<?php

/**
 * Main app controller.
 */
class AppController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            'https',
			'checkAuth - login, error, l10n, logo, verify',
            'postOnly + objectList',
            'ajaxOnly + objectList',
		);
	}

    /**
     * If user is logged in then redirect to a project list, otherwise
     * redirect to a login form.
     */
	public function actionIndex() {
        $this->redirect(array( 'project/index' ));
	}

    /**
     * Log the user in and redirect to a project list
     */
	public function actionLogin() {
        if (!Yii::app()->user->isGuest) {
            $this->redirect(array("project/index"));
        }

		$model = new LoginForm();

		// collect user input data
		if (isset($_POST["LoginForm"])) {
			$model->attributes = $_POST["LoginForm"];

			if ($model->validate()) {
                if ($model->login()) {
                    if (Yii::app()->user->getCertificateRequired()) {
                        $this->redirect(array("app/verify"));
                    } else {
                        $this->redirect("/");
                    }
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

		// display the login form
        $this->pageTitle = Yii::t("app", "Login");
		$this->render("login", array(
            "model" => $model
        ));
	}

    /**
     * Verify user's certificate, if needed
     */
	public function actionVerify() {
        /** @var WebUser $user */
        $user = Yii::app()->user;

        if ($user->isGuest) {
            $this->redirect(array("app/login"));
        }

        if (!$user->getCertificateRequired()) {
            $this->redirect(array("project/index"));
        }

        $user->setState("certificateVerified", false);

        $serial = $user->getCertificateSerial();
        $issuer = $user->getCertificateIssuer();
        $email = $user->getEmail();

        $validations = array(
            "SSL_CLIENT_VERIFY" => "SUCCESS",
            "SSL_CLIENT_M_SERIAL" => $serial,
            "SSL_CLIENT_I_DN" => $issuer,
            "SSL_CLIENT_S_DN_Email" => $email,
        );

        if ($serial && $issuer) {
            $failed = false;

            foreach ($validations as $key => $validator) {
                if (isset($_SERVER[$key]) && $_SERVER[$key] == $validator) {
                    continue;
                }

                if (isset($_SERVER["REDIRECT_" . $key]) && $_SERVER["REDIRECT_" . $key] == $validator) {
                    continue;
                }

                $failed = true;

                break;
            }


            if ($failed) {
                $user->logout();
                $user->setFlash("error", Yii::t("app", "Invalid client certificate."));
                $this->redirect(Yii::app()->homeUrl);

                return;
            }
        }

        $user->setState("certificateVerified", true);
        $this->redirect(array("project/index"));
	}

    /**
     * Log the user out and redirect to the main page
     */
	public function actionLogout() {
        Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Exception handler
	 */
	public function actionError() {
	    $error = Yii::app()->errorHandler->error;
        $this->breadcrumbs[] = array(Yii::t('app', 'Error'), '');

        if ($error) {
            $message = $error['message'];

            if ($error['code'] == 500) {
                $uniqueHash = strtoupper(substr(hash('sha256', time() . rand() . $error['message']), 0, 16));
                Yii::log($uniqueHash, 'error');

                $message = Yii::t('app', 'Internal server error. Please send this error code to the administrator - {code}.', array(
                    '{code}' => $uniqueHash
                ));
            }

            if (Yii::app()->request->isAjaxRequest) {
                echo $message;
            } else {
                $this->pageTitle = Yii::t('app', 'Error {code}', array( '{code}' => $error['code'] ));
                $this->render('error', array(
                    'message' => $message
                ));
            }
	    }
	}

    /**
     * Localization javascript file.
     */
    public function actionL10n() {
        header('Content-Type: text/javascript');
        echo $this->renderPartial('l10n');
    }

    /**
     * Constants javascript file.
     */
    public function actionConstants() {
        header("Content-Type: text/javascript");

        $classes = array(
            "Package",
            "Target",
            "TargetCheck",
            "TargetCheckEditForm",
        );

        $constants = array();

        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);
            $constants[$class] = $reflection->getConstants();
        }

        echo $this->renderPartial("constants", array(
            "constants" => $constants
        ));
    }

    /**
     * Object list.
     */
    public function actionObjectList()
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

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if ($language) {
                $language = $language->id;
            }

            $objects = array();

            switch ($model->operation)
            {
                case 'category-list':
                    $template  = RiskTemplate::model()->findByPk($model->id);

                    if (!$template)
                        throw new CHttpException(404, Yii::t('app', 'Template not found.'));

                    $criteria = new CDbCriteria();
                    $criteria->order = 'COALESCE(l10n.name, t.name) ASC';
                    $criteria->addColumnCondition(array(
                        't.risk_template_id' => $template->id
                    ));
                    $criteria->together = true;

                    $categories = RiskCategory::model()->with(array(
                        'l10n' => array(
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'l10n.language_id = :language_id',
                            'params'   => array(
                                'language_id' => $language,
                            ),
                        ),
                        'checks'
                    ))->findAll($criteria);

                    foreach ($categories as $category)
                    {
                        $checks = array();

                        foreach ($category->checks as $check)
                            $checks[$check->check_id] = array(
                                'likelihood' => $check->likelihood,
                                'damage'     => $check->damage
                            );

                        $objects[] = array(
                            'id'     => $category->id,
                            'name'   => CHtml::encode($category->localizedName),
                            'checks' => $checks
                        );
                    }

                    break;

                case 'project-list':
                    $client = Client::model()->findByPk($model->id);

                    if (!$client) {
                        throw new CHttpException(404, Yii::t('app', 'Client not found.'));
                    }

                    if (!$client->checkPermission()) {
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));
                    }

                    $criteria = new CDbCriteria();
                    $criteria->order = 't.name ASC, t.year ASC';
                    $criteria->addColumnCondition(array(
                        't.client_id' => $client->id
                    ));
                    $criteria->together = true;

                    if (User::checkRole(User::ROLE_ADMIN))
                        $projects = Project::model()->findAll($criteria);
                    else
                        $projects = Project::model()->with(array(
                            'projectUsers' => array(
                                'joinType' => 'INNER JOIN',
                                'on' => 'projectUsers.user_id = :user_id',
                                'params' => array(
                                    'user_id' => Yii::app()->user->id,
                                ),
                            ),
                        ))->findAll($criteria);

                    foreach ($projects as $project) {
                        $objects[] = array(
                            'id'   => $project->id,
                            'name' => CHtml::encode($project->name) . ' (' . $project->year . ')'
                        );
                    }

                    break;

                case 'target-list':
                    $project = Project::model()->findByPk($model->id);

                    if (!$project)
                        throw new CHttpException(404, Yii::t('app', 'Project not found.'));

                    if (!$project->checkPermission())
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));

                    $targets = Target::model()->findAllByAttributes(
                        array( 'project_id' => $project->id ),
                        array( 'order'      => 't.host ASC' )
                    );

                    foreach ($targets as $target)
                        $objects[] = array(
                            'id'   => $target->id,
                            'host' => $target->hostPort,
                        );

                    break;

                case "target-category-list":
                    $target = Target::model()->findByPk($model->id);

                    if (!$target) {
                        throw new CHttpException(404, Yii::t("app", "Project not found."));
                    }

                    if (!$target->project->checkPermission()) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    foreach ($target->_categories as $category) {
                        $objects[] = array(
                            "id" => $category->check_category_id,
                            "name" => $category->category->name,
                        );
                    }

                    break;

                case 'control-check-list':
                    $control = CheckControl::model()->findByPk($model->id);

                    if (!$control)
                        throw new CHttpException(404, Yii::t('app', 'Control not found.'));

                    $checks = Check::model()->with(array(
                        'l10n' => array(
                            'alias'    => 'l10n_c',
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'l10n_c.language_id = :language_id',
                            'params'   => array( 'language_id' => $language )
                        )
                    ))->findAllByAttributes(array(
                        'check_control_id' => $control->id
                    ));

                    foreach ($checks as $check)
                        $objects[] = array(
                            'id'   => $check->id,
                            'name' => $check->localizedName,
                        );

                    break;

                case 'target-check-list':
                    $targets = explode(',', $model->id);
                    $ratings = TargetCheck::getRatingNames();

                    foreach ($targets as $target) {
                        $target = (int) $target;
                        $target = Target::model()->with('project')->findByPk($target);

                        if (!$target) {
                            throw new CHttpException(404, Yii::t('app', 'Target not found.'));
                        }

                        if (!$target->project->checkPermission()) {
                            throw new CHttpException(403, Yii::t('app', 'Access denied.'));
                        }

                        $checkList = array();
                        $referenceIds = array();

                        $references = TargetReference::model()->findAllByAttributes(array(
                            'target_id' => $target->id
                        ));

                        foreach ($references as $reference)
                            $referenceIds[] = $reference->reference_id;

                        $targetData = array(
                            'id'          => $target->id,
                            'host'        => $target->host,
                            'description' => $target->description,
                            'checks'      => array()
                        );

                        foreach ($target->_categories as $category) {
                            $controlIds = array();

                            $controls = CheckControl::model()->findAllByAttributes(array(
                                'check_category_id' => $category->check_category_id
                            ));

                            foreach ($controls as $control)
                                $controlIds[] = $control->id;

                            $criteria = new CDbCriteria();

                            $criteria->order = 'COALESCE(l10n.name, t.name) ASC';
                            $criteria->addInCondition('t.reference_id', $referenceIds);
                            $criteria->addInCondition('t.check_control_id', $controlIds);
                            $criteria->together = true;

                            $checks = Check::model()->with(array(
                                'l10n' => array(
                                    'joinType' => 'LEFT JOIN',
                                    'on' => 'l10n.language_id = :language_id',
                                    'params' => array( 'language_id' => $language )
                                ),
                                'targetChecks' => array(
                                    'alias' => 'tcs',
                                    'joinType' => 'INNER JOIN',
                                    'on' => 'tcs.target_id = :target_id AND tcs.status = :status AND (tcs.rating = :med OR tcs.rating = :high)',
                                    'params' => array(
                                        'target_id' => $target->id,
                                        'status' => TargetCheck::STATUS_FINISHED,
                                        'med' => TargetCheck::RATING_MED_RISK,
                                        'high' => TargetCheck::RATING_HIGH_RISK
                                    ),
                                )
                            ))->findAll($criteria);

                            foreach ($checks as $check) {
                                $targetData['checks'][] = array(
                                    'id' => $check->id,
                                    'ratingName' => $ratings[$check->targetChecks[0]->rating],
                                    'rating' => $check->targetChecks[0]->rating,
                                    'name' => CHtml::encode($check->localizedName),
                                );
                            }
                        }

                        $objects[] = $targetData;
                    }

                    break;

                case "category-check-list":
                    $checks = Check::model()->with(array(
                        "control" => array(
                            "with" => array(
                                "category" => array(
                                    "alias" => "tcat",
                                    "joinType",
                                    "condition" => "tcat.id = :category_id",
                                    "params" => array(
                                        "category_id" => $model->id
                                    )
                                )
                            )
                        ),
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => array("language_id" => $language)
                        ),
                    ))->findAll();

                    foreach ($checks as $check) {
                        $objects[] = array(
                            "id" => $check->id,
                            "name" => CHtml::encode($check->localizedName),
                        );
                    }

                    break;

                case "category-control-list":
                    $category = CheckCategory::model()->findByPk($model->id);

                    if (!$category) {
                        throw new CHttpException(404, "Category not found.");
                    }

                    $controls = CheckControl::model()->with([
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => array("language_id" => $language)
                        ),
                    ])->findAllByAttributes(array(
                        "check_category_id" => $category->id
                    ));

                    foreach ($controls as $control) {
                        $objects[] = array(
                            "id" => $control->id,
                            "name" => CHtml::encode($control->localizedName),
                        );
                    }

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
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


    /**
     * Get logo.
     */
    public function actionLogo() {
        $filePath = Yii::app()->params["systemLogo"]["path"];
        $logoType = $this->_system->logo_type;

        if (!file_exists($filePath)) {
            $filePath = Yii::app()->params["systemLogo"]["defaultPath"];
            $logoType = "image/png";
        }

        // give user a file
        header('Content-Type: ' . $logoType);
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

    /**
     * Help
     */
    public function actionHelp() {
        $this->breadcrumbs[] = array(Yii::t("app", "Help"), "");
        $this->pageTitle = Yii::t("app", "Help");
		$this->render("help");
    }

    /**
     * Get file.
     */
    public function actionFile($section, $subsection, $file) {
        $filePath = implode("/", array(
            Yii::app()->params["filesPath"],
            $section,
            $subsection,
            $file
        ));

        if (!file_exists($filePath)) {
            throw new CHttpException(404, Yii::t("app", "File not found."));
        }

        // give user a file
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $file . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }
}
