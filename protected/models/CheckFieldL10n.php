<?php

/**
 * This is the model class for table "check_fields_l10n".
 *
 * The followings are the available columns in table 'check_fields_l10n':
 * @property integer $check_field_id
 * @property integer $language_id
 * @property string $value
 * @property CheckField $field
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

    /**
     * Get type
     * @return mixed|null
     */
    public function getType() {
        return $this->field->type;
    }

    /**
     * Get name
     * @return mixed|null
     */
    public function getName() {
        return $this->field->name;
    }

    /**
     * Set value
     * @param $value
     */
    public function setValue($value) {
        // case if checkbox, update all possible values
        if ($this->type == GlobalCheckField::TYPE_CHECKBOX) {
            $value = (int) $value;

            $criteria = new CDbCriteria();
            $criteria->addNotInCondition("language_id", [$this->language_id]);
            $criteria->addColumnCondition([
                "check_field_id" => $this->check_field_id
            ]);
            $l10ns = CheckFieldL10n::model()->findAll($criteria);

            foreach ($l10ns as $l10n) {
                $l10n->value = $value;
                $l10n->save();
            }

            $this->field->value = $value;
            $this->field->save();
        }

        $this->value = $value;

        $this->save();
    }
}
