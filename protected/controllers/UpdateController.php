<?php

/**
 * Update controller
 */
class UpdateController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "checkAdmin",
            "postOnly + status",
            "ajaxOnly + status",
            "idleOrUpdating",
		);
	}

    /**
     * Main page
     */
	public function actionIndex() {
        $system = System::model()->findByPk(1);
        $forbidUpdate = true;
        $forbidMessage = null;
        $updating = false;

        if ($system->status == System::STATUS_UPDATING) {
            $updating = true;
        } else {
            $backupTime = null;
            $backupLimit = new DateTime();
            $backupLimit->sub(new DateInterval("P1D"));

            if ($system->backup) {
                $backupTime = new DateTime($system->backup);
            }

            if ($system->status != System::STATUS_IDLE) {
                $forbidMessage = Yii::t(
                    "app",
                    "The system is busy. Please make sure that all running tasks are finished before proceeding."
                );
            } else if (!$backupTime || $backupTime < $backupLimit) {
                $forbidMessage = Yii::t(
                    "app",
                    "The system has been backed up more than 24 hours ago. Please download a backup before updating the system."
                );
            } else {
                $forbidUpdate = false;
            }
        }

        if (isset($_POST["UpdateForm"])) {
            if ($forbidUpdate) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $form = new UpdateForm();
            $form->attributes = $_POST["UpdateForm"];

            if (!$form->validate() || !$form->proceed || !$system->update_version) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            try {
                SystemManager::updateStatus(System::STATUS_UPDATING, System::STATUS_IDLE);
            } catch (Exception $e) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }

            $updating = true;
        }

        $req = Yii::app()->request;

        if ($system->update_version && (!isset($req->cookies["update_version"]) || $req->cookies["update_version"] != $system->update_version)) {
            $cookie = new CHttpCookie("update_version", $system->update_version);
            $cookie->path = "/";
            $cookie->secure = true;
            $cookie->expire = time() + 60 * 60 * 24 * 30;
            $req->cookies["update_version"] = $cookie;
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Update"), "");

		// display the page
        $this->pageTitle = Yii::t("app", "Update");
		$this->render("index", array(
            "system" => $system,
            "forbidUpdate" => $forbidUpdate,
            "forbidMessage" => $forbidMessage,
            "updating" => $updating,
        ));
    }

    /**
     * Update status page
     */
    public function actionStatus() {
        $response = new AjaxResponse();

        try {
            $system = System::model()->findByPk(1);
            $response->addData("updating", $system->status == System::STATUS_UPDATING);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}