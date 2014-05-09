<?php

/**
 * This is the model class for table "target_references".
 *
 * The followings are the available columns in table 'target_references':
 * @property integer $target_id
 * @property integer $reference_id
 */
class TargetReference extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetReference the static model class
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
		return 'target_references';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'target_id, reference_id', 'required' ),
            array( 'target_id, reference_id', 'numerical', 'integerOnly' => true ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'target'    => array( self::BELONGS_TO, 'Target',    'target_id'    ),
            'reference' => array( self::BELONGS_TO, 'Reference', 'reference_id' ),
		);
	}
}
