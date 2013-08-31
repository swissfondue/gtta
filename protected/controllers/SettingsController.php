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
        $system = System::model()->findByPk(1);

        $form->workstationId = $system->workstation_id;
        $form->workstationKey = $system->workstation_key;
        $form->timezone = $system->timezone;

        // collect form input data
		if (isset($_POST["SettingsEditForm"])) {
			$form->attributes = $_POST["SettingsEditForm"];

			if ($form->validate()) {
                $system->workstation_id = $form->workstationId;
                $system->workstation_key = $form->workstationKey;
                $system->timezone = $form->timezone;
                $system->save();

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
}