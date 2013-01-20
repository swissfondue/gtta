<?php

/**
 * History controller.
 */
class HistoryController extends Controller
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
		);
	}

    /**
     * Display login history
     */
	public function actionLogins($page=1)
	{
        $page = (int) $page;

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.create_time DESC';
        $criteria->together = true;

        $entries = LoginHistory::model()->with('user')->findAll($criteria);

        $entryCount = LoginHistory::model()->count($criteria);
        $paginator  = new Paginator($entryCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Login History'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Login History');
		$this->render('login', array(
            'entries' => $entries,
            'p'       => $paginator
        ));
	}
}
