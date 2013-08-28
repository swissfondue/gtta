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
        $forbidUpdate = false;
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
                $forbidUpdate = true;
                $forbidMessage = Yii::t(
                    "app",
                    "The system is busy. Please make sure that all running tasks are finished before proceeding."
                );
            } else if (!$backupTime || $backupTime < $backupLimit) {
                $forbidUpdate = true;
                $forbidMessage = Yii::t(
                    "app",
                    "The system has been backed up more than 24 hours ago. Please download a backup before updating the system."
                );
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

            $system->status = System::STATUS_UPDATING;
            $system->update_pid = null;
            $system->save();

            $updating = true;
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