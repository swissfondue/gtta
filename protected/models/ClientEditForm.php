<?php

/**
 * This is the model class for client edit form.
 */
class ClientEditForm extends CFormModel
{
	/**
     * @var string name.
     */
    public $name;

    /**
     * @var string country.
     */
    public $country;

    /**
     * @var string state.
     */
    public $state;

    /**
     * @var string city.
     */
    public $city;

    /**
     * @var string address.
     */
    public $address;

    /**
     * @var string postal code.
     */
    public $postcode;

    /**
     * @var string website.
     */
    public $website;

    /**
     * @var string contact name.
     */
    public $contactName;

    /**
     * @var string contact email.
     */
    public $contactEmail;

    /**
     * @var string contact phone.
     */
    public $contactPhone;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array( 'name', 'required' ),
            array( 'name, country, state, city, address, postcode, website, contactName, contactEmail, contactPhone', 'length', 'max' => 1000 ),
            array( 'contactEmail', 'email' ),
            array( 'website', 'url', 'defaultScheme' => 'http' ),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'         => Yii::t('app', 'Name'),
            'country'      => Yii::t('app', 'Country'),
            'state'        => Yii::t('app', 'State'),
            'city'         => Yii::t('app', 'City'),
            'address'      => Yii::t('app', 'Address'),
            'postcode'     => Yii::t('app', 'Postal Code'),
            'website'      => Yii::t('app', 'Website'),
            'contactName'  => Yii::t('app', 'Contact Name'),
            'contactPhone' => Yii::t('app', 'Contact Phone'),
            'contactEmail' => Yii::t('app', 'Contact E-mail'),
		);
	}
}