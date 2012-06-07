<?php

/**
 * Report controller.
 */
class ReportController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'checkAuth',
            'checkUser',
		);
	}

    /**
     * Main page of reports page.
     */
	public function actionIndex()
	{
        $this->breadcrumbs[Yii::t('app', 'Reports')] = '';

        // display the page
        $this->pageTitle = Yii::t('app', 'Reports');
		$this->render('index');
	}
}