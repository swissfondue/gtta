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
 * @property string $contact_fax
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
            array( 'name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email, contact_fax', 'length', 'max' => 1000 ),
            array( 'contact_email', 'email' ),
            array( 'website', 'url', 'defaultScheme' => 'http' ),
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
        return $this->contact_email || $this->contact_name || $this->contact_phone || $this->contact_fax;
    }

    /**
     * Check if user is permitted to access the client.
     */
    public function checkPermission()
    {
        $user = Yii::app()->user;

        if ($user->role == User::ROLE_ADMIN)
            return true;

        if ($user->role == User::ROLE_CLIENT && $user->client_id == $this->id)
            return true;

        if ($user->role == User::ROLE_USER)
        {
            $projects = Project::model()->findAllByAttributes(array(
                'client_id' => $this->id
            ));

            $projectIds = array();

            foreach ($projects as $project)
                $projectIds[] = $project->id;

            $criteria = new CDbCriteria();
            $criteria->addInCondition('project_id', $projectIds);
            $criteria->addColumnCondition(array(
                'user_id' => Yii::app()->user->id
            ));

            $check = ProjectUser::model()->findAll($criteria);

            if ($check)
                return true;
        }

        return false;
    }
}