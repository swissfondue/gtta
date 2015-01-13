<?php

/**
 * Background job class
 */
abstract class BackgroundJob {
    /**
     * Queue
     */
    const QUEUE_SYSTEM              = "system";
    const QUEUE_WORKER              = "worker";

    /**
     * System job flag
     */
    const SYSTEM = false;

    /**
     * Template for job's id
     */
    const ID_TEMPLATE = null;

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
        if (self::SYSTEM) {
            $this->id = "gtta.system." . md5(uniqid() . time());
        }

        if (isset($this->args["id"]) && $this->args["id"]) {
            $this->id = $this->args["id"];
        } else {
            $this->id = "gtta.worker." . md5(uniqid() . time());
        }

        $this->setVar("pid", posix_getpid());
    }

    /**
     * Delete job's keys
     */
    public function delKeys() {
        $keys = Resque::redis()->keys($this->id . ".*");
        $keys = explode(' ', $keys);

        foreach ($keys as $key) {
            JobManager::delKey($key);
        }
    }

    /**
     * Job tear down
     */
    public function tearDown() {
        $this->delKeys();
    }

    /**
     * Renders a template.
     */
    protected function render($template, $data = array()) {
        $path = Yii::getPathOfAlias($template).'.php';

        if (!file_exists($path))
            throw new Exception(Yii::t('app', 'Template {template} does not exist.', array(
                '{template}' => $path
            )));

        if(is_array($data))
            extract($data, EXTR_PREFIX_SAME, 'data');

        ob_start();
        ob_implicit_flush(false);
        require($path);

        return ob_get_clean();
    }

    /**
     * Create a job
     * @param $args
     */
    public static function enqueue($args = array()) {
        $job = get_called_class();
        $queue = $job::SYSTEM ? self::QUEUE_SYSTEM : self::QUEUE_WORKER;
        $id = JobManager::buildId($job::ID_TEMPLATE, $args);
        $token = Resque::enqueue($queue, $job, array_merge($args, array(
            "id" => $id
        )), true);

        Resque::redis()->set("$id.token", $token);
    }
}