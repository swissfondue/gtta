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
     * @var App System object
     */
    protected $_system = null;

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
        $system = System::model()->findByPk(1);

        if (!$system->timezone) {
            $system->timezone = "Europe/Zurich";
        }

        date_default_timezone_set($system->timezone);
        $this->_system = $system;

        if ($this::SYSTEM) {
            $this->id = "gtta.system." . md5(uniqid() . time());
        }

        if (isset($this->args["id"]) && $this->args["id"]) {
            $this->id = $this->args["id"];
        } else {
            $this->id = "gtta.worker." . md5(uniqid() . time());
        }

        $this->setVar("pid", posix_getpgid(getmypid()));
    }

    /**
     * Job tear down
     */
    public function tearDown() {
        $keys = Resque::redis()->keys($this->id . ".*");
        $keys = explode(' ', $keys);

        foreach ($keys as $key) {
            $key = str_replace('resque:', '', $key);
            Resque::redis()->del($key);
        }
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

        return $this->renderFile($path, $data, true);
    }

    /**
     * Renders a view file.
     * @param string $_viewFile_ view file path
     * @param array $_data_ optional data to be extracted as local view variables
     * @param boolean $_return_ whether to return the rendering result instead of displaying it
     * @return mixed the rendering result if required. Null otherwise.
     */
    public function renderFile($_viewFile_,$_data_=null,$_return_=false) {
        if(is_array($_data_))
            extract($_data_,EXTR_PREFIX_SAME,'data');
        else
            $data=$_data_;
        if($_return_)
        {
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        }
        else
            require($_viewFile_);
    }
}