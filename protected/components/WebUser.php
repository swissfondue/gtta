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

        $this->_model->last_action_time = new CDbExpression('NOW()');
        $this->_model->save();
    }
}
