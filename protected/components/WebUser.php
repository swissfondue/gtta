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
}
