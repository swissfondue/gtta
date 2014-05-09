<?php

/**
 * Base class for all application's controllers.
 */
class Controller extends CController {
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

        $system = System::model()->findByPk(1);

        $app = Yii::app();
        $lang = "en";

        if ($system->language) {
            $lang = $system->language->code;
        }

        if (isset($app->request->cookies["language"])) {
            $lang = $app->request->cookies["language"]->value;
        }

        if (!in_array($lang, array("en", "de"))) {
            $lang = "en";
        }

        $app->language = $lang;

        $this->_requestTime  = microtime(true);
        $this->breadcrumbs[] = array(Yii::t("app", "Home"), $this->createUrl("app/index"));

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
        /** @var WebUser $user */
        $user = Yii::app()->user;
        $user->loginUrl = $this->createUrl("app/login");

        if ($user->isGuest) {
            $user->loginRequired();
            return;
        }

        if ($user->getCertificateRequired() && !$user->getState("certificateVerified")) {
            $user->logout();
            $user->setFlash("error", Yii::t("app", "Invalid client certificate."));
            $this->redirect(Yii::app()->homeUrl);

            return;
        }

        // update last action time for logged in users
        $user->updateLastActionTime();

        $filterChain->run();
    }

    /**
     * If user is not user, display a 404 error
     */
    public function filterCheckUser($filterChain) {
        if (!User::checkRole(User::ROLE_USER)) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

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

    /**
     * Check if the system is in the requested status
     * @param $allowedStatuses
     * @throws CHttpException
     */
    protected function _checkSystemStatus($allowedStatuses) {
        $this->_system->refresh();

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

    /**
     * Check if system is IDLE or PACKAGE MANAGER is running
     * @param $filterChain
     * @throws CHttpException
     */
    public function filterIdleOrPackageManager($filterChain) {
        $this->_checkSystemStatus(array(System::STATUS_IDLE, System::STATUS_PACKAGE_MANAGER));
        $filterChain->run();
    }

    /**
     * Check if system is IDLE or in VM REGENERATE state
     * @param $filterChain
     * @throws CHttpException
     */
    public function filterIdleOrRegenerate($filterChain) {
        $this->_checkSystemStatus(array(System::STATUS_IDLE, System::STATUS_REGENERATE_SANDBOX));
        $filterChain->run();
    }

    /**
     * Check if system is IDLE or in VM REGENERATE state or in PACKAGE MANAGER state
     * @param $filterChain
     * @throws CHttpException
     */
    public function filterIdleOrRegenerateOrPackageManager($filterChain) {
        $this->_checkSystemStatus(array(System::STATUS_IDLE, System::STATUS_REGENERATE_SANDBOX, System::STATUS_PACKAGE_MANAGER));
        $filterChain->run();
    }
}
