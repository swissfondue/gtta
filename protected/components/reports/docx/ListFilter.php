<?php

/**
 * Template list filter evaluator
 */
class ListFilter {
    /**
     * Filter variable
     * @var null|mixed
     */
    private $_variable = null;

    /**
     * Filter parameters
     * @var null|ParameterParser
     */
    private $_params = null;

    /**
     * Scope
     * @var VariableScope scope
     */
    private $_scope;

    /**
     * Constructor
     * @param $filter
     * @param VariableScope $scope
     * @throws Exception
     */
    public function __construct($filter, VariableScope $scope) {
        $openingBrace = mb_strpos($filter, "(");
        $closingBrace = mb_strpos($filter, ")");

        if ($openingBrace === false || $closingBrace === false) {
            throw new Exception(Yii::t("app", "Filter parameters missing."));
        }

        $this->_variable = mb_substr($filter, 0, $openingBrace);
        $params = mb_substr($filter, $openingBrace + 1, $closingBrace - $openingBrace - 1);
        $this->_params = new ParameterParser($params);
        $this->_scope = $scope;
    }

    /**
     * Apply filter
     * @param array $list
     * @return array
     * @throws Exception
     */
    public function apply($list) {
        $resultList = array();

        /** @var IVariableScopeObject $item */
        foreach ($list as $item) {
            if (!$item instanceof IVariableScopeObject) {
                throw new Exception(Yii::t("app", "List item does not implement IVariableScopeObject interface."));
            }

            $result = false;
            $value = $item->getVariable($this->_variable, $this->_scope);

            switch ($this->_params->type) {
                case ParameterParser::TYPE_SCALAR:
                    if ($value == $this->_params->value) {
                        $result = true;
                    }

                    break;

                case ParameterParser::TYPE_LIST:
                    if (in_array($value, $this->_params->value)) {
                        $result = true;
                    }

                    break;

                case ParameterParser::TYPE_RANGE:
                    if ($value >= $this->_params->value[0] && $value <= $this->_params->value[1]) {
                        $result = true;
                    }

                    break;
            }

            if ($this->_params->negate) {
                $result = !$result;
            }

            if ($result) {
                $resultList[] = $item;
            }
        }

        return $resultList;
    }
}
