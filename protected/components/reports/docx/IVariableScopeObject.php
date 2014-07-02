<?php

/**
 * Variable scope object interface
 */
interface IVariableScopeObject {
    /**
     * Get variable value
     * @param $name
     * @param VariableScope $scope
     * @return mixed
     */
    public function getVariable($name, VariableScope $scope);

    /**
     * Get list
     * @param $name
     * @param $filters
     * @param VariableScope $scope
     * @return array
     */
    public function getList($name, $filters, VariableScope $scope);
}
