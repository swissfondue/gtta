<?php

/**
 * Class RelationTemplate
 *
 * The followings are the available columns in table 'checks':
 * @property integer $id
 * @property string  $name
 * @property string  $relations
 */
class RelationTemplate extends ActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Check the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return "relation_templates";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array("name", "required"),
            array("name", "length", "max" => 1000),
            array("relations", "safe"),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            "l10n" => array(self::HAS_MANY, "RelationTemplateL10n", "relation_template_id"),
        );
    }

    /**
     * @return string localized name.
     */
    public function getLocalizedName() {
        if ($this->l10n && count($this->l10n) > 0) {
            return $this->l10n[0]->name != NULL ? $this->l10n[0]->name : $this->name;
        }

        return $this->name;
    }
}