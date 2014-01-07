<?php

/**
 * Settings controller.
 */
class SettingsController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "checkAdmin",
            "idleOrRunning",
		);
	}

    /**
     * Edit settings
     */
	public function actionEdit() {
        $form = new SettingsEditForm();

        /** @var System $system  */
        $system = System::model()->findByPk(1);

        $form->workstationId = $system->workstation_id;
        $form->workstationKey = $system->workstation_key;
        $form->timezone = $system->timezone;
        $form->reportLowPedestal = $system->report_low_pedestal;
        $form->reportMedPedestal = $system->report_med_pedestal;
        $form->reportHighPedestal = $system->report_high_pedestal;
        $form->reportMaxRating = $system->report_max_rating;
        $form->reportMedDampingLow = $system->report_med_damping_low;
        $form->reportHighDampingLow = $system->report_high_damping_low;
        $form->reportHighDampingMed = $system->report_high_damping_med;
        $form->copyright = $system->copyright;

        // collect form input data
		if (isset($_POST["SettingsEditForm"])) {
			$form->attributes = $_POST["SettingsEditForm"];

			if ($form->validate()) {
                $system->workstation_id = $form->workstationId ? $form->workstationId : null;
                $system->workstation_key = $form->workstationKey ? $form->workstationKey : null;
                $system->timezone = $form->timezone;
                $system->report_low_pedestal = $form->reportLowPedestal;
                $system->report_med_pedestal = $form->reportMedPedestal;
                $system->report_high_pedestal = $form->reportHighPedestal;
                $system->report_max_rating = $form->reportMaxRating;
                $system->report_med_damping_low = $form->reportMedDampingLow;
                $system->report_high_damping_low = $form->reportHighDampingLow;
                $system->report_high_damping_med = $form->reportHighDampingMed;
                $system->copyright = $form->copyright;
                $system->demo = true;

                $system->save();
                $this->_system->refresh();

                Yii::app()->user->setFlash("success", Yii::t("app", "Settings saved."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Settings"), "");

		// display the page
        $this->pageTitle = Yii::t("app", "Settings");
		$this->render("edit", array(
            "form" => $form,
            "system" => $system
        ));
    }

    /**
     * Upload logo.
     */
    function actionUploadLogo() {
        $response = new AjaxResponse();

        try {
            $model = new SystemLogoUploadForm();
            $model->image = CUploadedFile::getInstanceByName("SystemLogoUploadForm[image]");

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $model->image->saveAs(Yii::app()->params["systemLogo"]["path"]);
            $this->_system->logo_type = $model->image->type;
            $this->_system->save();

            $response->addData("url", $this->createUrl("app/logo"));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control logo.
     */
    public function actionControlLogo() {
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

            if (!@file_exists(Yii::app()->params["systemLogo"]["path"])) {
                throw new CHttpException(404, Yii::t("app", "Logo not found."));
            }

            switch ($model->operation) {
                case "delete":
                    @unlink(Yii::app()->params["systemLogo"]["path"]);
                    $this->_system->logo_type = null;
                    $this->_system->save();

                    $response->addData("url", $this->createUrl("app/logo"));

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