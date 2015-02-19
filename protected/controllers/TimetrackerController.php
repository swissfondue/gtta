<?php

/**
 * Time tracker controller.
 */
class TimetrackerController extends Controller {
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
     * Display the tracker.
     */
	public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->order = "t.deadline DESC, t.hours_allocated DESC";
        $criteria->together = true;
        $projects = Project::model()->with(array(
            "projectUsers" => array(
                "with" => "user"
            ),
            "client",
        ))->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t("app", "Time Tracker"), "");
        $this->pageTitle = Yii::t("app", "Time Tracker");
		$this->render("index", array(
            "projects" => $projects,
        ));
	}
}
