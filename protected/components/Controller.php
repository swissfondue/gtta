<?php

/**
 * Base class for all application's controllers.
 */
class Controller extends CController 
{
    /**
     * @var array breadcrumbs.
     */
    public $breadcrumbs = array();

    /**
     * Controller initialization.
     */
    function init()
    {
        parent::init();

        $app  = Yii::app();
        $lang = 'en';

        if (isset($app->request->cookies['language']))
            $lang = $app->request->cookies['language']->value;

        if (!in_array($lang, array( 'en', 'de' )))
            $lang = 'en';

        $app->language = $lang;

        $this->breadcrumbs[] = array(Yii::t('app', 'Home'), $this->createUrl('app/index'));
    }

    /**
     * If user is not authenticated, redirect to the login page.
     */
    public function filterCheckAuth($filterChain)
    {
        Yii::app()->user->loginUrl = $this->createUrl('app/login');

        if (Yii::app()->user->isGuest)
        {
            Yii::app()->user->loginRequired();
            return;
        }

        $filterChain->run();
    }

    /**
     * If user is not user, display a 404 error
     */
    public function filterCheckUser($filterChain)
    {
        if (!User::checkRole(User::ROLE_USER))
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $filterChain->run();
    }
    
    /**
     * If user is not admin, display a 404 error
     */
    public function filterCheckAdmin($filterChain)
    {
        if (!User::checkRole(User::ROLE_ADMIN))
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $filterChain->run();
    }
}
