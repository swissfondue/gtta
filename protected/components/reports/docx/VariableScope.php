<?php

/**
 * Scope
 */
class VariableScope {
    /**
     * Constants
     */
    const SCOPE_PROJECT = "project";
    const SCOPE_TARGET = "target";
    const SCOPE_CATEGORY = "category";
    const SCOPE_CONTROL = "control";
    const SCOPE_CHECK = "check";

    /**
     * @var string name
     */
    private $_name = null;

    /**
     * @var mixed object
     */
    private $_object = null;

    /**
     * @var VariableScopeStack scope stack reference
     */
    private $_stack = null;

    /**
     * Get valid scopes
     * @return array
     */
    public static function getValidScopes() {
        return array(
            self::SCOPE_PROJECT,
            self::SCOPE_TARGET,
            self::SCOPE_CATEGORY,
            self::SCOPE_CONTROL,
            self::SCOPE_CHECK,
        );
    }

    /**
     * Constructor
     * @param string $name
     * @param IVariableScopeObject $object
     * @param VariableScopeStack $stack
     * @throws Exception
     */
    public function __construct($name, IVariableScopeObject $object, VariableScopeStack &$stack) {
        if (!in_array($name, self::getValidScopes())) {
            throw new Exception(Yii::t("app", "Invalid scope: {scope}.", array("{scope}" => $name)));
        }

        $this->_name = $name;
        $this->_object = $object;
        $this->_stack = $stack;
    }

    /**
     * Get name
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Get scope object variable
     * @param $name
     * @return array
     * @throws Exception
     */
    public function getVariable($name) {
        return $this->_object->getVariable($name, $this);
    }

    /**
     * Get scope object list
     * @param $name
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function getList($name, $filters) {
        return $this->_object->getList($name, $filters, $this);
    }

    /**
     * Get scope object
     * @return IVariableScopeObject
     */
    public function getObject() {
        return $this->_object;
    }

    /**
     * Get scope stack
     * @return VariableScopeStack
     */
    public function getStack() {
        return $this->_stack;
    }
}