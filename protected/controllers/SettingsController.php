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
                $system->demo = true;
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