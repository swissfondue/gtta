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
     * @var int request time.
     */
    protected $_requestTime = 0;

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

        $this->_requestTime  = microtime(true);
        $this->breadcrumbs[] = array(Yii::t('app', 'Home'), $this->createUrl('app/index'));

        $system = System::model()->findAll();

        if ($system) {
            $system = $system[0];
        } else {
            $system = new System();
        }

        if (!$system->timezone) {
            $system->timezone = "Europe/Zurich";
        }

        date_default_timezone_set($system->timezone);
    }

    /**
     * Render template.
     */
    public function render($view, $data=NULL, $return=false)
    {
        $this->_requestTime = microtime(true) - $this->_requestTime;
        return parent::render($view, $data, $return);
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

        // update last action time for logged in users
        Yii::app()->user->updateLastActionTime();

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

    /**
     * HTTPS filter.
     */
    public function filterHttps($filterChain)
    {
        if (!Yii::app()->getRequest()->isSecureConnection)
        {
            $url = 'https://' .
                Yii::app()->getRequest()->serverName .
                Yii::app()->getRequest()->requestUri;
                Yii::app()->request->redirect($url);

            return;
        }

        $filterChain->run();
    }
}
