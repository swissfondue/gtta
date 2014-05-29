<?php

/**
 * This is the model class for table "targets".
 *
 * The followings are the available columns in table 'targets':
 * @property integer $id
 * @property integer $project_id
 * @property string $host
 * @property string $description
 * @property TargetCheck[] $targetChecks
 */
class Target extends ActiveRecord {
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
            array( 'host, description', 'length', 'max' => 1000 ),
            array( 'project_id', 'numerical', 'integerOnly' => true ),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "_categories" => array(self::HAS_MANY, "TargetCheckCategory", "target_id"),
            "categories" => array(self::MANY_MANY, "CheckCategory", "target_check_categories(target_id, check_category_id)"),
            "_references" => array(self::HAS_MANY, "TargetReference", "target_id"),
            "references" => array(self::MANY_MANY, "Reference", "target_references(target_id, reference_id)"),
            "checkCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(check_count)"),
            "finishedCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(finished_count)"),
            "infoCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(info_count)"),
            "lowRiskCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(low_risk_count)"),
            "medRiskCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(med_risk_count)"),
            "highRiskCount" => array(self::STAT, "TargetCheckCategory", "target_id", "select" => "SUM(high_risk_count)"),
            "targetChecks" => array(self::HAS_MANY, "TargetCheck", "target_id"),
        );
	}

    /**
     * Synchronize target checks (delete old ones and create new).
     */
    public function syncChecks() {
        $checkIds = array();
        $referenceIds = array();

        $references = TargetReference::model()->findAllByAttributes(array(
            'target_id' => $this->id
        ));

        foreach ($references as $reference) {
            $referenceIds[] = $reference->reference_id;
        }

        $categories = TargetCheckCategory::model()->with('category')->findAllByAttributes(array(
            'target_id' => $this->id
        ));

        foreach ($categories as $category) {
            $controlIds = array();

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            foreach ($controls as $control) {
                $controlIds[] = $control->id;
            }

            $criteria = new CDbCriteria();

            if (!$category->advanced) {
                $criteria->addCondition('t.advanced = FALSE');
            }

            $criteria->addInCondition('t.check_control_id', $controlIds);
            $criteria->addInCondition('t.reference_id', $referenceIds);
            $checks = Check::model()->findAll($criteria);

            foreach ($checks as $check) {
                $checkIds[] = $check->id;
            }
        }

        // clean target checks
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('check_id', $checkIds);
        $criteria->addColumnCondition(array(
            'target_id' => $this->id
        ));

        TargetCheck::model()->deleteAll($criteria);
        $cache = array();

        foreach (TargetCheck::model()->findAllByAttributes(array("target_id" => $this->id)) as $tc) {
            $cache[] = $tc->check_id;
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        foreach ($checkIds as $checkId) {
            if (in_array($checkId, $cache)) {
                continue;
            }

            $check = new TargetCheck();
            $check->target_id = $this->id;
            $check->check_id = $checkId;
            $check->status = TargetCheck::STATUS_OPEN;
            $check->rating = TargetCheck::RATING_NONE;
            $check->user_id = Yii::app()->user->id;
            $check->language_id = $language;
            $check->save();
        }
    }
}
