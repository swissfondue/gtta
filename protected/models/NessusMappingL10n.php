<?php

/**
 * This is the model class for table "nessus_mappings_l10n".
 *
 * The followings are the available columns in table 'nessus_mappings_l10n':
 * @property integer $nessus_mapping_id
 * @property integer $language_id
 * @property string $name
 */
class NessusMappingL10n extends ActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className
     * @return mixed
     */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "nessus_mappings_l10n";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return [
            ["nessus_mapping_id, language_id", "required"],
            ["name", "length", "max" => 1000],
            ["nessus_mapping_id, language_id", "numerical", "integerOnly" => true],
		];
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return [
            "mapping" => [self::BELONGS_TO, "NessusMapping", "nessus_mapping_id"],
            "language" => [self::BELONGS_TO, "Language", "language_id"],
		];
	}
}
