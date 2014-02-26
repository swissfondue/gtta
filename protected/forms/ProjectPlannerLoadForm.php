<?php

/**
 * This is the model class for loading project planner data.
 */
class ProjectPlannerLoadForm extends CFormModel {
    /**
     * @var string start date
     */
    public $startDate;

    /**
     * @var string end date
     */
    public $endDate;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array("startDate, endDate", "required"),
            array("startDate, endDate", "date", "format" => "yyyy-MM-dd"),
		);
	}
}