<?php

/**
 * This is the model class for table "system".
 *
 * The followings are the available columns in table "system":
 * @property integer $id
 * @property string $backup
 * @property string $timezone
 * @property string $workstation_id
 * @property string $workstation_key
 * @property string $version
 * @property string $version_description
 * @property string $update_version
 * @property string $update_description
 * @property string $update_check_time
 * @property string $update_time
 */
class System extends CActiveRecord {
    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return System the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "system";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("workstation_id, workstation_key, version, update_version, update_description, version_description", "length", "max" => 1000),
            array("backup, timezone, update_check_time, update_time", "safe"),
		);
	}
}