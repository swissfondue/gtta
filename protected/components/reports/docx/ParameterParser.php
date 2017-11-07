<?php

/**
 * Parameter parser
 */
class ParameterParser {
    /**
     * Constants
     */
    const TYPE_SCALAR = 0;
    const TYPE_LIST = 1;
    const TYPE_RANGE = 2;

    /**
     * Negate parameter
     * @var bool
     */
    public $negate = false;

    /**
     * Parameter type
     * @var null|integer
     */
    public $type = null;

    /**
     * Parsed parameter value
     * @var null|mixed
     */
    public $value = null;

    /**
     * Parameter parsing
     * @param $param
     * @throws Exception
     */
    public function __construct($param) {
        if (mb_substr($param, 0, 1) === "!") {
            $this->negate = true;
            $param = mb_substr($param, 1);
        }

        if (preg_match('/^[a-zA-Z0-9]+$/', $param)) {
            $this->type = self::TYPE_SCALAR;
            $this->value = $param;
        } else if (preg_match('/^d+\.\d+\.\.\d+\.\d+$/', $param) || preg_match('/^\d+\.\.\d+$/', $param)) {
            $this->type = self::TYPE_RANGE;
            $this->value = explode("..", $param);
        } else if (mb_strpos($param, ",") !== false) {
            $this->type = self::TYPE_LIST;
            $this->value = explode(",", $param);
        } else {
            throw new Exception(Yii::t("app", "Invalid parameter: {param}.", array("{param}" => $param)));
        }
    }
} 