<?php

/**
 * This is the model class for table "clients".
 *
 * The followings are the available columns in table 'clients':
 * @property integer $id
 * @property string $name
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $address
 * @property string $postcode
 * @property string $website
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_email
 */
class Client extends CActiveRecord
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
		return 'clients';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'name', 'required' ),
            array( 'name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email', 'length', 'max' => 1000 ),
            array( 'contact_email', 'email' ),
            array( 'website', 'url', 'defaultScheme' => 'http' ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'projectCount' => array( self::STAT, 'Project', 'client_id' ),
		);
	}

    /**
     * Check if client has details.
     */
    public function getHasDetails()
    {
        return $this->country || $this->city || $this->state || $this->address || $this->postcode || $this->website;
    }

    /**
     * Check if client has contact info.
     */
    public function getHasContact()
    {
        return $this->contact_email || $this->contact_name || $this->contact_phone;
    }
}