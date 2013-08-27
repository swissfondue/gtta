<?php

/**
 * Monitor controller.
 */
class MonitorController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "checkAdmin",
            "postOnly + controlprocess",
            "ajaxOnly + controlprocess",
		);
	}

    /**
     * Show running processes.
     */
	public function actionProcesses() {
        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition("t.status", array(
            TargetCheck::STATUS_IN_PROGRESS,
            TargetCheck::STATUS_STOP
        ));
        $criteria->order = "COALESCE(l10n.name, \"check\".name) ASC";
        $criteria->together = true;

        $checks = TargetCheck::model()->with(array(
            "target" => array(
                "with" => "project"
            ),
            "check" => array(
                "with" => array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "l10n.language_id = :language_id",
                        "params" => array("language_id" => $language)
                    ),
                )
            ),
            "user",
        ))->findAll($criteria);

        $criteria = new CDbCriteria();
        $criteria->addInCondition("t.status", array(
            ProjectGtCheck::STATUS_IN_PROGRESS,
            ProjectGtCheck::STATUS_STOP
        ));
        $criteria->order = "COALESCE(l10n.name, \"innerCheck\".name) ASC";
        $criteria->together = true;

        $gtChecks = ProjectGtCheck::model()->with(array(
            "check" => array(
                "with" => array(
                    "check" => array(
                        "alias" => "innerCheck",
                        "with" => array(
                            "l10n" => array(
                                "joinType" => "LEFT JOIN",
                                "on" => "l10n.language_id = :language_id",
                                "params" => array("language_id" => $language)
                            ),
                        )
                    )
                )
            )    
        ))->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t("app", "Running Processes"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Running Processes");
		$this->render("processes", array(
            "checks" => $checks,
            "gtChecks" => $gtChecks,
        ));
    }

    /**
     * Control process function
     */
    public function actionControlProcess() {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST["EntryControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $ids = explode("-", $model->id);
            $isGuided = false;
            $check = null;

            if ($ids[0] == "gt") {
                $isGuided = true;
                $project = (int) $ids[1];
                $check = (int) $ids[2];

                $project = Project::model()->findByPk($project);

                if (!$project) {
                    throw new CHttpException(404, Yii::t("app", "Project not found."));
                }

                $check = GtCheck::model()->findByPk($check);

                if (!$check) {
                    throw new CHttpException(404, Yii::t("app", "Check not found."));
                }

                $check = ProjectGtCheck::model()->findByAttributes(array(
                    "project_id" => $project->id,
                    "gt_check_id" => $check->id
                ));
            } else {
                $target = (int) $ids[0];
                $check = (int) $ids[1];

                $target = Target::model()->findByPk($target);

                if (!$target) {
                    throw new CHttpException(404, Yii::t("app", "Target not found."));
                }

                $check = Check::model()->findByPk($check);

                if (!$check) {
                    throw new CHttpException(404, Yii::t("app", "Check not found."));
                }

                $check = TargetCheck::model()->findByAttributes(array(
                    "target_id" => $target->id,
                    "check_id" => $check->id
                ));
            }

            if (!$check) {
                throw new CHttpException(404, Yii::t("app", "Process not found."));
            }

            switch ($model->operation) {
                case "stop":
                    $class = new ReflectionClass(get_class($check));

                    if ($check->status != $class->getConstant("STATUS_IN_PROGRESS")) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    $check->status = $class->getConstant("STATUS_STOP");
                    $check->save();

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

    /**
     * List of active user sessions
     */
    public function actionSessions() {
        $interval = new DateTime();
        $interval->sub(new DateInterval("PT10M"));

        $criteria = new CDbCriteria();
        $criteria->addCondition("t.last_action_time > :interval");
        $criteria->params = array('interval' => $interval->format("Y-m-d H:i:s"));
        $criteria->order = 't.role ASC, t.name ASC, t.email ASC';

        $users = User::model()->findAll($criteria);
        $this->breadcrumbs[] = array(Yii::t('app', 'Active Sessions'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Active Sessions');
		$this->render('sessions', array(
            'users' => $users,
            'roles' => array(
                User::ROLE_ADMIN  => Yii::t('app', 'Admin'),
                User::ROLE_USER   => Yii::t('app', 'User'),
                User::ROLE_CLIENT => Yii::t('app', 'Client'),
            ),
        ));
    }
}