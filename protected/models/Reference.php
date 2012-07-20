<?php

/**
 * This is the model class for table "references".
 *
 * The followings are the available columns in table 'references':
 * @property integer $id
 * @property string $name
 * @property string $url
 */
class Reference extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Reference the static model class
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
		return 'references';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name', 'required' ),
            array( 'name, url', 'length', 'max' => 1000 ),
		);
	}
}