<?php

/**
 * This is the model class for table "target_check_categories".
 *
 * The followings are the available columns in table "target_check_categories":
 * @property integer $target_id
 * @property integer $check_category_id
 * @property boolean $advanced
 * @property integer $check_count
 * @property integer $finished_count
 * @property integer $low_risk_count
 * @property integer $med_risk_count
 * @property integer $high_risk_count
 * @property integer $info_count
 * @property CheckCategory $category
 */
class TargetCheckCategory extends ActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TargetCheckCategory the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "target_check_categories";
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("target_id, check_category_id", "required"),
            array("target_id, check_category_id", "numerical", "integerOnly" => true),
            array("advanced", "boolean"),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            "target" => array(self::BELONGS_TO, "Target", "target_id"),
            "category" => array(self::BELONGS_TO, "CheckCategory", "check_category_id"),
		);
	}

    /**
     * Update stats.
     */
    public function updateStats() {
        $controlIds = array();
        $referenceIds = array();
        $checkCount = 0;
        $finishedCount = 0;
        $infoCount = 0;
        $lowCount = 0;
        $medCount = 0;
        $highCount = 0;

        $controls = CheckControl::model()->with("customChecks")->findAllByAttributes(array(
             "check_category_id" => $this->check_category_id
        ));

        foreach ($controls as $control) {
            $controlIds[] = $control->id;

            $customChecks = TargetCustomCheck::model()->findAllByAttributes(array(
                "check_control_id" => $control->id,
                "target_id" => $this->target_id,
            ));

            foreach ($customChecks as $custom) {
                $checkCount++;
                $finishedCount++;

                switch ($custom->rating) {
                    case TargetCustomCheck::RATING_INFO:
                        $infoCount++;
                        break;

                    case TargetCustomCheck::RATING_LOW_RISK:
                        $lowCount++;
                        break;

                    case TargetCustomCheck::RATING_MED_RISK:
                        $medCount++;
                        break;

                    case TargetCustomCheck::RATING_HIGH_RISK:
                        $highCount++;
                        break;

                    default:
                        break;
                }
            }
        }

        $references = TargetReference::model()->findAllByAttributes(array(
            "target_id" => $this->target_id
        ));

        foreach ($references as $reference) {
            $referenceIds[] = $reference->reference_id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition("check_control_id", $controlIds);
        $criteria->addInCondition("reference_id", $referenceIds);

        if (!$this->advanced) {
            $criteria->addCondition("t.advanced = FALSE");
        }

        $checkCount += Check::model()->count($criteria);

        $checks = Check::model()->findAll($criteria);
        $checkIds = array();

        foreach ($checks as $check) {
            $checkIds[] = $check->id;
        }

        $criteria = new CDbCriteria();

        $criteria->addColumnCondition(array(
            "target_id" => $this->target_id,
            "status" => TargetCheck::STATUS_FINISHED
        ));

        $criteria->addInCondition("check_id", $checkIds);
        $finishedCount += TargetCheck::model()->count($criteria);

        // info count
        $infoCriteria = clone $criteria;
        $infoCriteria->addColumnCondition(array("rating" => TargetCheck::RATING_INFO));
        $infoCount += TargetCheck::model()->count($infoCriteria);
        
        // low count
        $lowCriteria = clone $criteria;
        $lowCriteria->addColumnCondition(array("rating" => TargetCheck::RATING_LOW_RISK));
        $lowCount += TargetCheck::model()->count($lowCriteria);

        // med count
        $medCriteria = clone $criteria;
        $medCriteria->addColumnCondition(array("rating" => TargetCheck::RATING_MED_RISK));
        $medCount += TargetCheck::model()->count($medCriteria);

        // high count
        $highCriteria = clone $criteria;
        $highCriteria->addColumnCondition(array("rating" => TargetCheck::RATING_HIGH_RISK));
        $highCount += TargetCheck::model()->count($highCriteria);

        $this->check_count = $checkCount;
        $this->finished_count = $finishedCount;
        $this->info_count = $infoCount;
        $this->low_risk_count = $lowCount;
        $this->med_risk_count = $medCount;
        $this->high_risk_count = $highCount;

        $this->save();
    }

    /**
     * Update all stats.
     */
    static public function updateAllStats() {
        $targetCategories = TargetCheckCategory::model()->findAll();

        foreach ($targetCategories as $targetCategory) {
            $targetCategory->updateStats();
            sleep(1);
        }
    }
}
