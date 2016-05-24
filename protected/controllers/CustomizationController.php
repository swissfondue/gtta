<?php

/**
 * Customization controller.
 */
class CustomizationController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'https',
            'checkAuth',
            'checkAdmin',
            'ajaxOnly + control, controlcontrol, controlcheck, controlresult, controlsolution, controlinput, controlscript',
            'postOnly + control, controlcontrol, controlcheck, controlresult, controlsolution, controlinput, controlscript'
        );
    }

    /**
     * Categories list
     */
    public function actionIndex() {
        $this->breadcrumbs[] = array(Yii::t("app", "Customization"), "");

        $this->pageTitle = Yii::t("app", "Customization");
        $this->render("index", []);
    }

    /**
     * Checks customization categories
     */
    public function actionChecks() {
        $this->breadcrumbs[] = array(Yii::t("app", "Customization"), $this->createUrl("customization/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Checks"), "");

        $this->pageTitle = Yii::t("app", "Checks");
        $this->render("check/index", []);
    }

    public function actionCheckFields($page = 1) {}
}

