<?php

/**
 * This is the model class for table "check_solutions".
 *
 * The followings are the available columns in table 'check_solutions':
 * @property integer $id
 * @property integer $check_id
 * @property string $title
 * @property string $solution
 * @property integer $sort_order
 * @property integer $max_sort_order
 */
class CheckSolution extends ActiveRecord
{
    /**
     * @var integer max sort order.
     */
    public $max_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckSolution the static model class
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
		return 'check_solutions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'title, solution, check_id', 'required' ),
            array( 'sort_order', 'numerical', 'integerOnly' => true, 'min' => 0 ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'  => array( self::HAS_MANY,   'CheckSolutionL10n', 'check_solution_id' ),
            'check' => array( self::BELONGS_TO, 'Check',             'check_id' ),
		);
	}

    /**
     * @return string localized title.
     */
    public function getLocalizedTitle()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->title != NULL ? $this->l10n[0]->title : $this->title;

        return $this->title;
    }

    /**
     * @return string localized solution.
     */
    public function getLocalizedSolution()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->solution != NULL ? $this->l10n[0]->solution : $this->solution;

        return $this->solution;
    }
}
