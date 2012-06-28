<?php

/**
 * This is the model class for table "check_inputs".
 *
 * The followings are the available columns in table 'check_inputs':
 * @property integer $id
 * @property integer $check_id
 * @property string $name
 * @property string $description
 * @property string $value
 * @property integer $sort_order
 * @property integer $max_sort_order
 */
class CheckInput extends CActiveRecord
{
    /**
     * @var integer max sort order.
     */
    public $max_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckInput the static model class
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
		return 'check_inputs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'check_id, name', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'sort_order', 'numerical', 'integerOnly' => true, 'min' => 0 ),
            array( 'description, value', 'safe' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'  => array( self::HAS_MANY,   'CheckInputL10n', 'check_input_id' ),
            'check' => array( self::BELONGS_TO, 'Check',          'check_id' ),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->name == NULL ? $this->l10n[0]->name : $this->name;

        return $this->name;
    }

    /**
     * @return string localized description.
     */
    public function getLocalizedDescription()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->description == NULL ? $this->l10n[0]->description : $this->description;

        return $this->description;
    }

    /**
     * @return string localized value.
     */
    public function getLocalizedValue()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->value == NULL ? $this->l10n[0]->value : $this->value;

        return $this->value;
    }
}