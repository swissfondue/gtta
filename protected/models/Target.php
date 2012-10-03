<?php

/**
 * This is the model class for table "targets".
 *
 * The followings are the available columns in table 'targets':
 * @property integer $id
 * @property integer $project_id
 * @property string $host
 */
class Target extends CActiveRecord
{   
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Target the static model class
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
		return 'targets';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'host, project_id', 'required' ),
            array( 'host', 'length', 'max' => 1000 ),
            array( 'project_id', 'numerical', 'integerOnly' => true ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'project'       => array( self::BELONGS_TO, 'Project',             'project_id' ),
            '_categories'   => array( self::HAS_MANY,   'TargetCheckCategory', 'target_id'  ),
            'categories'    => array( self::MANY_MANY,  'CheckCategory',       'target_check_categories(target_id, check_category_id)' ),
            '_references'   => array( self::HAS_MANY,   'TargetReference',     'target_id'  ),
            'references'    => array( self::MANY_MANY,  'Reference',           'target_references(target_id, reference_id)'    ),
            'checkCount'    => array( self::STAT,       'TargetCheckCategory', 'target_id', 'select' => 'SUM(check_count)'     ),
            'finishedCount' => array( self::STAT,       'TargetCheckCategory', 'target_id', 'select' => 'SUM(finished_count)'  ),
            'lowRiskCount'  => array( self::STAT,       'TargetCheckCategory', 'target_id', 'select' => 'SUM(low_risk_count)'  ),
            'medRiskCount'  => array( self::STAT,       'TargetCheckCategory', 'target_id', 'select' => 'SUM(med_risk_count)'  ),
            'highRiskCount' => array( self::STAT,       'TargetCheckCategory', 'target_id', 'select' => 'SUM(high_risk_count)' ),
            'vulns'         => array( self::HAS_MANY,   'TargetCheckVuln',     'target_id' ),
		);
	}

    /**
     * Clean target checks (delete old ones).
     */
    public function cleanChecks()
    {
        $checkIds = array();
        $referenceIds = array();

        $references = TargetReference::model()->findAllByAttributes(array(
            'target_id' => $this->id
        ));

        foreach ($references as $reference)
            $referenceIds[] = $reference->reference_id;

        $categories = TargetCheckCategory::model()->with('category')->findAllByAttributes(array(
            'target_id' => $this->id
        ));

        foreach ($categories as $category)
        {
            $controlIds = array();

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            foreach ($controls as $control)
                $controlIds[] = $control->id;

            $criteria = new CDbCriteria();

            if (!$category->advanced)
                $criteria->addCondition('t.advanced = FALSE');

            $criteria->addInCondition('t.check_control_id', $controlIds);
            $criteria->addInCondition('t.reference_id', $referenceIds);

            $checks = Check::model()->findAll($criteria);

            foreach ($checks as $check)
                $checkIds[] = $check->id;
        }

        // clean target checks
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('check_id', $checkIds);
        $criteria->addColumnCondition(array(
            'target_id' => $this->id
        ));

        TargetCheck::model()->deleteAll($criteria);
    }
}