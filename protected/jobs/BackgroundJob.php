<?php

/**
 * Background job class
 */
abstract class BackgroundJob {
    /**
     * System job flag
     */
    const SYSTEM = false;

    /**
     * @var string|null job id
     */
    protected $id = null;

    /**
     * @var array args
     */
    public $args = array();

    /**
     * Set job shared variable
     * @param $var
     * @param $value
     */
    protected function setVar($var, $value) {
        Resque::redis()->set($this->id . "." . $var, $value);
    }

    /**
     * Get job shared variable
     * @param $var
     * @return string|null
     */
    protected function getVar($var) {
        return Resque::redis()->get($this->id . "." . $var);
    }

    /**
     * Job set up
     */
    public function setUp() {
        if ($this::SYSTEM) {
            $this->id = "gtta.system." . md5(uniqid() . time());
        } if (isset($this->args["id"]) && $this->args["id"]) {
            $this->id = $this->args["id"];
        } else {
            $this->id = "gtta.worker." . md5(uniqid() . time());
        }

        $this->setVar("pid", posix_getpid());
    }

    /**
     * Job tear down
     */
    public function tearDown() {
        Resque::redis()->del($this->id . ".*");
    }
}