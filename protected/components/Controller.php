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
     * @var System system
     */
    protected $_system = null;

    /**
     * Controller initialization.
     */
    function init() {
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

        $system = System::model()->findByPk(1);

        if (!$system->timezone) {
            $system->timezone = "Europe/Zurich";
        }

        date_default_timezone_set($system->timezone);
        $this->_system = $system;
    }

    /**
     * Render template.
     */
    public function render($view, $data=null, $return=false)
    {
        $this->_requestTime = microtime(true) - $this->_requestTime;
        return parent::render($view, $data, $return);
    }

    /**
     * If user is not authenticated, redirect to the login page.
     */
    public function filterCheckAuth($filterChain) {
        Yii::app()->user->loginUrl = $this->createUrl('app/login');

        if (Yii::app()->user->isGuest) {
            Yii::app()->user->loginRequired();
            return;
        }

        if (Yii::app()->user->getCertificateRequired()) {
            $serial = Yii::app()->user->getCertificateSerial();
            $issuer = Yii::app()->user->getCertificateIssuer();
            $email = Yii::app()->user->getEmail();

            if ($serial &&
                $issuer && (
                    !isset($_SERVER["SSL_CLIENT_VERIFY"]) || $_SERVER["SSL_CLIENT_VERIFY"] != "SUCCESS" ||
                    !isset($_SERVER["SSL_CLIENT_M_SERIAL"]) || $serial != $_SERVER["SSL_CLIENT_M_SERIAL"] ||
                    !isset($_SERVER["SSL_CLIENT_I_DN"]) || $issuer != $_SERVER["SSL_CLIENT_I_DN"] ||
                    !isset($_SERVER["SSL_CLIENT_S_DN_Email"]) || $email != $_SERVER["SSL_CLIENT_S_DN_Email"]
                )
            ) {
                Yii::app()->user->logout();
		        $this->redirect(Yii::app()->homeUrl);

                return;
            }
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

    /**
     * Show reports filter.
     */
    public function filterShowReports($filterChain) {
        if (User::checkRole(User::ROLE_CLIENT) && !Yii::app()->user->getShowReports()) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        $filterChain->run();
    }

    /**
     * Show details filter.
     */
    public function filterShowDetails($filterChain) {
        if (User::checkRole(User::ROLE_CLIENT) && !Yii::app()->user->getShowDetails()) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        $filterChain->run();
    }

    private function _checkSystemStatus($allowedStatuses) {
        if (!is_array($allowedStatuses)) {
            $allowedStatuses = array($allowedStatuses);
        }

        if (!in_array($this->_system->status, $allowedStatuses)) {
            throw new CHttpException(
                403,
                $this->_system->getStringStatus() . " " .
                Yii::t("app", "Please wait until all running tasks are finished before proceeding.")
            );
        }
    }

    /**
     * Check if system is IDLE or RUNNING
     * @param $filterChain
     * @throws CHttpException
     */
    public function filterIdleOrRunning($filterChain) {
        $this->_checkSystemStatus(array(System::STATUS_IDLE, System::STATUS_RUNNING));
        $filterChain->run();
    }

    /**
     * Check if system status is IDLE
     * @param $filterChain
     * @throws CHttpException
     */
    public function filterIdle($filterChain) {
        $this->_checkSystemStatus(System::STATUS_IDLE);
        $filterChain->run();
    }

    /**
     * Check if system is IDLE or UPDATING
     * @param $filterChain
     * @throws CHttpException
     */
    public function filterIdleOrUpdating($filterChain) {
        $this->_checkSystemStatus(array(System::STATUS_IDLE, System::STATUS_UPDATING));
        $filterChain->run();
    }
}
