<?php

/**
 * Variable evaluator
 */
class VariableEvaluator {
    /**
     * Variable value
     * @var null
     */
    private $_value = null;

    /**
     * Constructor
     * @param $name
     * @param VariableScope $scope
     * @throws Exception
     */
    public function __construct($name, VariableScope $scope) {
        $scopeName = null;

        if (mb_strpos($name, ".") !== false) {
            $data = explode(".", $name);

            if (count($data) > 2) {
                throw new Exception(Yii::t("app", "Only one scope level is supported."));
            }

            list($scopeName, $name) = $data;
        }

        $this->_value = $scope->getStack()->get($scopeName)->getVariable($name, $scope);
    }

    /**
     * Evaluate variable
     * @return string
     */
    public function evaluate() {
        return $this->_value;
    }
}