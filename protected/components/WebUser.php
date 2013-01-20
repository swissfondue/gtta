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
    protected function loadUserModel()
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

        $this->loadUserModel();

        return $this->_model->role;
    }

    /**
     * Get client id.
     */
    public function getClient_id()
    {
        if ($this->isGuest)
            return null;

        $this->loadUserModel();

        return $this->_model->client_id;
    }

    /**
     * Update last action time.
     */
    public function updateLastActionTime()
    {
        if ($this->isGuest)
            return null;

        $this->loadUserModel();

        $this->_model->last_action_time = new CDbExpression('NOW()');
        $this->_model->save();
    }
}
