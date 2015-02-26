<?php

/**
 * Class TargetManager
 */
class TargetManager {
    /**
     * Update stats
     * @param TargetCheckCategory $category
     */
    public static function updateTargetCategoryStats(TargetCheckCategory $category) {
        $controlIds = array();
        $checkCount = 0;
        $finishedCount = 0;
        $infoCount = 0;
        $lowCount = 0;
        $medCount = 0;
        $highCount = 0;

        $target = $category->target;
        $controls = CheckControl::model()->findAllByAttributes(array(
            "check_category_id" => $category->check_category_id
        ));

        foreach ($controls as $control) {
            $controlIds[] = $control->id;

            $customChecks = TargetCustomCheck::model()->findAllByAttributes(array(
                "check_control_id" => $control->id,
                "target_id" => $category->target_id,
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

        $criteria = new CDbCriteria();
        $criteria->addInCondition("tc.check_control_id", $controlIds);

        if (!$target->checklist_templates) {
            $referenceIds = array();
            $references = TargetReference::model()->findAllByAttributes(array(
                "target_id" => $category->target_id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            $criteria->addInCondition("tc.reference_id", $referenceIds);
        }

        $criteria->addColumnCondition(array(
            "t.target_id" => $target->id
        ));

        if (!$category->advanced) {
            $criteria->addCondition("tc.advanced = FALSE");
        }

        $checkCount += TargetCheck::model()->with(array(
            "check" => array(
                "alias" => "tc",
                "joinType" => "LEFT JOIN"
            )
        ))->count($criteria);

        $criteria = new CDbCriteria();
        $criteria->addInCondition("check_control_id", $controlIds);

        if (!$target->checklist_templates) {
            $criteria->addInCondition("reference_id", $referenceIds);
        }

        if (!$category->advanced) {
            $criteria->addCondition("t.advanced = FALSE");
        }

        $checks = Check::model()->findAll($criteria);
        $checkIds = array();

        foreach ($checks as $check) {
            $checkIds[] = $check->id;
        }

        $criteria = new CDbCriteria();

        $criteria->addColumnCondition(array(
            "target_id" => $category->target_id,
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

        $category->check_count = $checkCount;
        $category->finished_count = $finishedCount;
        $category->info_count = $infoCount;
        $category->low_risk_count = $lowCount;
        $category->med_risk_count = $medCount;
        $category->high_risk_count = $highCount;
        $category->save();
    }

    /**
     * Reindex checks by target check category
     * @param TargetCheckCategory $category
     */
    public static function reindexTargetCategoryChecks(TargetCheckCategory $category) {
        $controlIds = array();
        $checkIds = array();
        $target = $category->target;

        $referenceIds = array();
        $references = TargetReference::model()->findAllByAttributes(array(
            "target_id" => $category->target_id
        ));

        foreach ($references as $reference) {
            $referenceIds[] = $reference->reference_id;
        }

        $controls = CheckControl::model()->findAllByAttributes(array(
            "check_category_id" => $category->check_category_id
        ));

        foreach ($controls as $control) {
            $controlIds[] = $control->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition("check_control_id", $controlIds);
        $criteria->addInCondition("reference_id", $referenceIds);

        if (!$category->advanced) {
            $criteria->addCondition("t.advanced = FALSE");
        }

        $checks = Check::model()->findAll($criteria);

        $targetChecks = TargetCheck::model()->findAllByAttributes(array("target_id" => $category->target_id));

        foreach ($targetChecks as $check) {
            $checkIds[] = $check->check_id;
        }

        // clean target checks
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition("check_id", $checkIds);
        $criteria->addColumnCondition(array(
            "target_id" => $target->id
        ));
        TargetCheck::model()->deleteAll($criteria);

        $admin = null;

        foreach ($category->target->project->projectUsers as $user) {
            if ($user->admin) {
                $admin = $user->user_id;
                break;
            }
        }

        if ($admin == null) {
            $admin = User::model()->findByAttributes(array("role" => User::ROLE_ADMIN));

            if ($admin) {
                $admin = $admin->id;
            }
        }

        $language = Language::model()->findByAttributes(array("default" => true));

        foreach ($checks as $check) {
            if (in_array($check->id, $checkIds)) {
                continue;
            }

            $targetCheck = new TargetCheck();
            $targetCheck->target_id = $category->target_id;
            $targetCheck->check_id = $check->id;
            $targetCheck->user_id = $admin;
            $targetCheck->language_id = $language->id;
            $targetCheck->status = TargetCheck::STATUS_OPEN;
            $targetCheck->rating = TargetCheck::RATING_NONE;
            $targetCheck->save();
        }
    }

    /**
     * Reindex checks by target checklist template
     * @param TargetChecklistTemplate $template
     */
    public static function reindexTargetTemplateChecks(TargetChecklistTemplate $template) {
        $checks = array();
        $checkIds = array();
        $target = $template->target;

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            "t.checklist_template_id" => $template->checklist_template_id,
        ));

        $templateChecks = ChecklistTemplateCheck::model()->with(array(
            "check" => array(
                "alias" => "tc",
                "joinType" => "LEFT JOIN",
            )
        ))->findAll($criteria);

        foreach ($templateChecks as $tc) {
            $checks[] = $tc->check;

            $targetCheck = TargetCheck::model()->findByAttributes(array(
                "target_id" => $target->id,
                "check_id"  => $tc->check_id,
            ));

            if ($targetCheck) {
                $checkIds[] = $targetCheck->check_id;
            }
        }

        // clean target checks
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition("check_id", $checkIds);
        $criteria->addColumnCondition(array(
            "target_id" => $target->id
        ));
        TargetCheck::model()->deleteAll($criteria);

        $admin = null;

        foreach ($template->target->project->projectUsers as $user) {
            if ($user->admin) {
                $admin = $user->user_id;
                break;
            }
        }

        if ($admin == null) {
            $admin = User::model()->findByAttributes(array("role" => User::ROLE_ADMIN));

            if ($admin) {
                $admin = $admin->id;
            }
        }

        $language = Language::model()->findByAttributes(array("default" => true));

        foreach ($checks as $check) {
            if (in_array($check->id, $checkIds) && !$target->canAddCheck($check->id)) {
                continue;
            }

            $targetCheck = new TargetCheck();
            $targetCheck->target_id = $target->id;
            $targetCheck->check_id = $check->id;
            $targetCheck->user_id = $admin;
            $targetCheck->language_id = $language->id;
            $targetCheck->status = TargetCheck::STATUS_OPEN;
            $targetCheck->rating = TargetCheck::RATING_NONE;
            $targetCheck->save();
        }
    }
}