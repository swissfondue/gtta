<?php

/**
 * This is the model class for table "checks".
 *
 * The followings are the available columns in table 'checks':
 * @property integer $id
 * @property integer $check_control_id
 * @property string $name
 * @property string $background_info
 * @property string $hints
 * @property string $question
 * @property boolean $advanced
 * @property boolean $automated
 * @property string $script
 * @property boolean $multiple_solutions
 * @property string $protocol
 * @property integer $port
 * @property integer $reference_id
 * @property string $reference_code
 * @property string $reference_url
 * @property integer $effort
 * @property integer $sort_order
 */
class Check extends CActiveRecord
{
    // nearest sort order
    public $nearest_sort_order;

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
            array( 'name, check_control_id, sort_order', 'required' ),
            array( 'name, script, protocol, reference_code, reference_url', 'length', 'max' => 1000 ),
            array( 'check_control_id, reference_id, port, effort, sort_order', 'numerical', 'integerOnly' => true ),
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
            'control'                => array( self::BELONGS_TO, 'CheckControl',          'check_control_id' ),
            '_reference'             => array( self::BELONGS_TO, 'Reference',             'reference_id'     ),
            'targetChecks'           => array( self::HAS_MANY,   'TargetCheck',           'check_id' ),
            'targetCheckInputs'      => array( self::HAS_MANY,   'TargetCheckInput',      'check_id' ),
            'targetCheckSolutions'   => array( self::HAS_MANY,   'TargetCheckSolution',   'check_id' ),
            'targetCheckAttachments' => array( self::HAS_MANY,   'TargetCheckAttachment', 'check_id' ),
            'results'                => array( self::HAS_MANY,   'CheckResult',           'check_id' ),
            'solutions'              => array( self::HAS_MANY,   'CheckSolution',         'check_id' ),
            'inputs'                 => array( self::HAS_MANY,   'CheckInput',            'check_id' ),
            'riskCategories'         => array( self::HAS_MANY,   'RiskCategoryCheck',     'check_id' ),
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
     * @return string localized question.
     */
    public function getLocalizedQuestion()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->question != NULL ? $this->l10n[0]->question : $this->question;

        return $this->question;
    }

    /**
     * @return boolean is running.
     */
    public function getIsRunning()
    {
        return $this->automated && $this->targetChecks && in_array($this->targetChecks[0]->status, array( TargetCheck::STATUS_IN_PROGRESS, TargetCheck::STATUS_STOP ));
    }
}