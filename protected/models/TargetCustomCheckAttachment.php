<?php

/**
 * This is the model class for table "target_custom_check_attachments".
 *
 * The followings are the available columns in table 'target_custom_check_attachments':
 * @property integer $target_custom_check_id
 * @property string $name
 * @property string $type
 * @property string $path
 * @property integer $size
 * @property TargetCustomCheck $targetCustomCheck
 */
class TargetCustomCheckAttachment extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCustomCheckAttachment the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "target_custom_check_attachments";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("target_custom_check_id, name, type, path, size", "required"),
            array("target_custom_check_id, size", "numerical", "integerOnly" => true),
            array("name, type, path", "length", "max" => 1000),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "targetCustomCheck" => array(self::BELONGS_TO, "TargetCustomCheck", "target_custom_check_id"),
		);
	}
}
