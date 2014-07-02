<?php

/**
 * Condition evaluator
 */
class ConditionEvaluator {
    /**
     * Condition variable value
     * @var null|mixed
     */
    private $_value = null;

    /**
     * Condition parameters
     * @var null|ParameterParser
     */
    private $_params = null;

    /**
     * Constructor
     * @param $condition
     * @param VariableScope $scope
     * @throws Exception
     */
    public function __construct($condition, VariableScope $scope) {
        $openingBrace = mb_strpos($condition, "(");
        $closingBrace = mb_strpos($condition, ")");

        if ($openingBrace === false || $closingBrace === false) {
            throw new Exception(Yii::t("app", "Condition parameters missing."));
        }

        $params = mb_substr($condition, $openingBrace + 1, $closingBrace - $openingBrace - 1);
        $this->_params = new ParameterParser($params);
        $name = mb_substr($condition, 0, $openingBrace);
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
     * Evaluate condition
     * @return boolean
     * @throws Exception
     */
    public function evaluate() {
        $result = false;

        switch ($this->_params->type) {
            case ParameterParser::TYPE_SCALAR:
                if ($this->_value == $this->_params->value) {
                    $result = true;
                }

                break;

            case ParameterParser::TYPE_LIST:
                if (in_array($this->_value, $this->_params->value)) {
                    $result = true;
                }

                break;

            case ParameterParser::TYPE_RANGE:
                if ($this->_value >= $this->_params->value[0] && $this->_value <= $this->_params->value[1]) {
                    $result = true;
                }

                break;
        }

        if ($this->_params->negate) {
            $result = !$result;
        }

        return $result;
    }
}
