<?php

/**
 * This is the model class for table "checks".
 *
 * The followings are the available columns in table 'checks':
 * @property integer $id
 * @property integer $check_category_id
 * @property string $name
 * @property string $background_info
 * @property string $hints
 * @property string $reference
 * @property string $question
 * @property boolean $advanced
 * @property boolean $automated
 * @property string $script
 * @property boolean $multiple_solutions
 * @property string $protocol
 * @property integer $port
 */
class Check extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Check the static model class
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
		return 'checks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name, check_category_id', 'required' ),
            array( 'name, script, protocol', 'length', 'max' => 1000 ),
            array( 'check_category_id, port', 'numerical', 'integerOnly' => true ),
            array( 'advanced, automated, multiple_solutions', 'boolean' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'                   => array( self::HAS_MANY,   'CheckL10n',             'check_id' ),
            'category'               => array( self::BELONGS_TO, 'CheckCategory',         'check_category_id' ),
            'targetChecks'           => array( self::HAS_MANY,   'TargetCheck',           'check_id' ),
            'targetCheckInputs'      => array( self::HAS_MANY,   'TargetCheckInput',      'check_id' ),
            'targetCheckSolutions'   => array( self::HAS_MANY,   'TargetCheckSolution',   'check_id' ),
            'targetCheckAttachments' => array( self::HAS_MANY,   'TargetCheckAttachment', 'check_id' ),
            'results'                => array( self::HAS_MANY,   'CheckResult',           'check_id' ),
            'solutions'              => array( self::HAS_MANY,   'CheckSolution',         'check_id' ),
            'inputs'                 => array( self::HAS_MANY,   'CheckInput',            'check_id' ),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->name != NULL ? $this->l10n[0]->name : $this->name;

        return $this->name;
    }

    /**
     * @return string localized background info.
     */
    public function getLocalizedBackgroundInfo()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->background_info != NULL ? $this->l10n[0]->background_info : $this->background_info;

        return $this->background_info;
    }

    /**
     * @return string localized hints.
     */
    public function getLocalizedHints()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->hints != NULL ? $this->l10n[0]->hints : $this->hints;

        return $this->hints;
    }
    
    /**
     * @return string localized reference.
     */
    public function getLocalizedReference()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->reference != NULL ? $this->l10n[0]->reference : $this->reference;

        return $this->reference;
    }
    
    /**
     * @return string localized question.
     */
    public function getLocalizedQuestion()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->question != NULL ? $this->l10n[0]->question : $this->question;

        return $this->question;
    }
}