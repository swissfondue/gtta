<?php

/**
 * This is the model class for table "packages".
 *
 * The followings are the available columns in table "checks":
 * @property integer $id
 * @property string $file_name
 * @property integer $type
 * @property boolean $system
 * @property string $name
 * @property string $version
 * @property integer $status
 * @property integer $external_id
 * @property string $create_time
 * @property Package[] $dependencies
 */
class Package extends ActiveRecord {
    const TYPE_LIBRARY = 0;
    const TYPE_SCRIPT = 1;
    const STATUS_INSTALL = 0;
    const STATUS_INSTALLED = 1;
    const STATUS_SHARE = 2;
    const STATUS_DELETE = 10;
    const STATUS_ERROR = 100;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Package the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "packages";
	}

    /**
     * Get active package statuses
     * @return array
     */
    public static function getActiveStatuses() {
        return array(self::STATUS_INSTALLED, self::STATUS_SHARE);
    }

    /**
     * Check if package status is active
     * @return boolean
     */
    public function isActive() {
        return in_array($this->status, self::getActiveStatuses());
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("type, name, version", "required"),
            array("file_name, name, version", "length", "max" => 1000),
            array("type, status, external_id", "numerical", "integerOnly" => true),
            array("type", "in", "range" => array(self::TYPE_LIBRARY, self::TYPE_SCRIPT)),
            array("status", "in", "range" => array(
                self::STATUS_INSTALL,
                self::STATUS_INSTALLED,
                self::STATUS_SHARE,
                self::STATUS_DELETE,
                self::STATUS_ERROR
            )),
            array("system", "boolean"),
            array("create_time", "safe"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "packageDependencies" => array(self::HAS_MANY, "PackageDependency", "from_package_id"),
            "dependencies" => array(self::MANY_MANY, "Package", "package_dependencies(from_package_id, to_package_id)"),
            "dependents" => array(self::MANY_MANY, "Package", "package_dependencies(to_package_id, from_package_id)"),
		);
	}

    /**
     * Get status name
     * @return string
     */
    public function getStatusName() {
        $names = array(
            self::STATUS_INSTALL => Yii::t("app", "Installing"),
            self::STATUS_INSTALLED => Yii::t("app", "Installed"),
            self::STATUS_SHARE => Yii::t("app", "Sharing"),
            self::STATUS_DELETE => Yii::t("app", "Deleting"),
            self::STATUS_ERROR => Yii::t("app", "Error"),
        );

        if (!isset($names[$this->status])) {
            throw new Exception(Yii::t("app", "Invalid status."));
        }

        return $names[$this->status];
    }
}
