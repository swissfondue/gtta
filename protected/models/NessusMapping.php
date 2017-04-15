<?php

/**
 * This is the model class for table "nessus_mappings".
 *
 * The followings are the available columns in table "nessus_mappings":
 * @property integer $id
 * @property string $name
 * @property string $date
 * @property NessusMappingVuln $vulns
 */
class NessusMapping extends ActiveRecord {
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
        return "nessus_mappings";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ["name", "required"],
            ["name", "length", "max" => 1000],
            ["created_at", "safe"]
        ];
    }

    /**
     * Model relations
     * @return array
     */
    public function relations() {
        return [
            "vulns" => [self::HAS_MANY, "NessusMappingVuln", "nessus_mapping_id"],
        ];
    }

    /**
     * Get mapping hosts
     * @return array
     */
    public function getHosts() {
        $hosts = [];

        $criteria = new CDbCriteria();
        $criteria->select = "nessus_host";
        $criteria->group = "nessus_host";
        $criteria->addColumnCondition([
            "nessus_mapping_id" => $this->id
        ]);
        $criteria->order = "nessus_host ASC";

        $records = NessusMappingVuln::model()->findAll($criteria);

        foreach ($records as $r) {
            $hosts[] = $r->nessus_host;
        }

        return $hosts;
    }
}