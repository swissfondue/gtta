<?php

/**
 * This is the model class for table "check_results".
 *
 * The followings are the available columns in table 'check_results':
 * @property integer $id
 * @property integer $check_id
 * @property string $result
 * @property integer $sort_order
 */
class CheckResult extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckResult the static model class
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
		return 'check_results';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'result, check_id', 'required' ),
            array( 'sort_order', 'numerical', 'integerOnly' => true, 'min' => 0 ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n'  => array( self::HAS_MANY,   'CheckResultL10n', 'check_result_id' ),
            'check' => array( self::BELONGS_TO, 'Check',           'check_id' ),
		);
	}

    /**
     * @return string localized result.
     */
    public function getLocalizedResult()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->result == NULL ? $this->l10n[0]->result : $this->result;

        return $this->result;
    }
}