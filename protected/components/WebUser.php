<?php

/**
 * Web user class.
 */
class WebUser extends CWebUser
{
    /**
     * @var User model storage.
     */
    private $_model;

    /**
     * Load model.
     */
    protected function _loadUserModel()
    {
        if ($this->isGuest)
            return;

        if ($this->_model)
            return;

        $this->_model = User::model()->findByPk($this->id);
    }

    /**
     * Get role.
     */
    public function getRole()
    {
        if ($this->isGuest)
            return null;

        $this->_loadUserModel();

        return $this->_model->role;
    }

    /**
     * Get client id.
     */
    public function getClient_id()
    {
        if ($this->isGuest)
            return null;

        $this->_loadUserModel();

        return $this->_model->client_id;
    }

    /**
     * Get show reports
     */
    public function getShowReports() {
        if ($this->isGuest) {
            return null;
        }

        $this->_loadUserModel();

        return $this->_model->show_reports;
    }

    /**
     * Get show details
     */
    public function getShowDetails() {
        if ($this->isGuest) {
            return null;
        }

        $this->_loadUserModel();

        return $this->_model->show_details;
    }

    /**
     * Update last action time.
     */
    public function updateLastActionTime()
    {
        if ($this->isGuest)
            return null;

        $this->_loadUserModel();

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

        $now = new DateTime();
        $this->_model->last_action_time = $now->format("Y-m-d H:i:s");
        $this->_model->save();
    }

    /**
     * Get certificate required.
     */
    public function getCertificateRequired() {
        if ($this->isGuest) {
            return null;
        }

        $this->_loadUserModel();

        return $this->_model->certificate_required;
    }

    /**
     * Get certificate serial number.
     */
    public function getCertificateSerial() {
        if ($this->isGuest) {
            return null;
        }

        $this->_loadUserModel();

        return $this->_model->certificate_serial;
    }

    /**
     * Get certificate issuer DN.
     */
    public function getCertificateIssuer() {
        if ($this->isGuest) {
            return null;
        }

        $this->_loadUserModel();

        return $this->_model->certificate_issuer;
    }

    /**
     * Get email.
     */
    public function getEmail() {
        if ($this->isGuest) {
            return null;
        }

        $this->_loadUserModel();

        return $this->_model->email;
    }
}
