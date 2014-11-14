<?php

/**
 * This is the model class for table "languages".
 *
 * The followings are the available columns in table 'languages':
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property boolean $default
 */
class Language extends ActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Client the static model class
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
		return 'languages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name, code', 'required' ),
            array( 'default', 'boolean' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'code', 'length', 'max' => 2 )
		);
	}

    /**
     * Set language as user's default language
     */
    public function setUserDefault() {
        $default = self::model()->findByAttributes(array(
            'user_default' => true
        ));

        if ($default) {
            $default->user_default = false;
            $default->save();
        }

        $this->user_default = true;
        $this->save();
    }
}
