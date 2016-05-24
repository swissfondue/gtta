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

    public function actionIndex($page = 1) {
    }
}

