<?php

/**
 * This is the model class for table "references".
 *
 * The followings are the available columns in table "references":
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $external_id
 * @property integer status
 */
class Reference extends ActiveRecord {
    const STATUS_INSTALLED = 1;
    const STATUS_SHARE = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Reference the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "references";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("name", "required"),
            array("name, url", "length", "max" => 1000),
            array("external_id, status", "numerical", "integerOnly" => true),
            array("status", "in", "range" => array(
                self::STATUS_INSTALLED,
                self::STATUS_SHARE,
            )),
		);
	}
}
