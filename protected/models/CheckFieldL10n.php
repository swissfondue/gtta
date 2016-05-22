<?php

/**
 * This is the model class for table "check_fields_l10n".
 *
 * The followings are the available columns in table 'check_fields_l10n':
 * @property integer $check_field_id
 * @property integer $language_id
 * @property string $name
 * @property string $title
 * @property string $value
 */
class CheckFieldL10n extends ActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CheckInputL10n the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return "check_fields_l10n";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            ["check_field_id, language_id", "required"],
            ["name, title", "length", "max" => 1000],
            ["check_field_id, language_id", "numerical", "integerOnly" => true],
            ["value", "safe"],
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            "field" => [self::BELONGS_TO, "CheckField", "check_field_id"],
            "language"   => [self::BELONGS_TO, "Language",   "language_id"],
        ];
    }
}
