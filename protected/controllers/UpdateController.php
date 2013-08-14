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
		);
	}

    /**
     * Main page
     */
	public function actionIndex() {
        $system = System::model()->findByPk(1);
        $this->breadcrumbs[] = array(Yii::t("app", "Update"), "");

		// display the page
        $this->pageTitle = Yii::t("app", "Update");
		$this->render("index", array(
            "system" => $system
        ));
    }
}