<?php

/**
 * Scope stack
 */
class VariableScopeStack {
    /**
     * Scope stack
     * @var array stack
     */
    private $_stack = array();

    /**
     * Push scope to stack
     * @param string $name
     * @param IVariableScopeObject $object
     * @throws Exception
     */
    public function push($name, $object) {
        if (!$object instanceof IVariableScopeObject) {
            throw new Exception(Yii::t("app", "List item does not implement IVariableScopeObject interface."));
        }

        array_push($this->_stack, new VariableScope($name, $object, $this));
    }

    /**
     * Pop scope from stack
     * @return VariableScope
     * @throws Exception
     */
    public function pop() {
        if (count($this->_stack) == 0) {
            throw new Exception(Yii::t("app", "Empty scope stack."));
        }

        return array_pop($this->_stack);
    }

    /**
     * Get scope
     * @param string|null $name
     * @return VariableScope
     * @throws Exception
     */
    public function get($name=null) {
        if (count($this->_stack) == 0) {
            throw new Exception(Yii::t("app", "Empty scope stack."));
        }

        $scope = null;

        // if scope name is not specified, then return the current scope
        if ($name === null) {
            $scope = call_user_func("end", array_values($this->_stack));
        } else {
            /** @var VariableScope $s */
            foreach (array_reverse($this->_stack) as $s) {
                if ($s->getName() == $name) {
                    $scope = $s;
                    break;
                }
            }
        }

        if ($scope == null) {
            throw new Exception(Yii::t("app", "Invalid scope name."));
        }

        return $scope;
    }
}