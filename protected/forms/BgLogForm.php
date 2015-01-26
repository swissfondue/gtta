<?php

/**
 * This is the model class for getting background job log.
 */
class BgLogForm extends CFormModel {
    /**
     * @var string job.
     */
    public $job;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("job", "required"),
            array("job", "in", "range" => JobManager::$jobs),
        );
    }
}