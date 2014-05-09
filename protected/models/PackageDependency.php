<?php

/**
 * This is the model class for table "package_dependencies".
 *
 * The followings are the available columns in table "package_dependencies":
 * @property integer $from_package_id
 * @property integer $to_package_id
 */
class PackageDependency extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PackageDependency the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "package_dependencies";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("from_package_id, to_package_id", "required"),
            array("from_package_id, to_package_id", "numerical", "integerOnly" => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "dependent" => array(self::BELONGS_TO, "Package", "from_package_id"),
            "dependency" => array(self::BELONGS_TO, "Package", "to_package_id"),
		);
	}
}
