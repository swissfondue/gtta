<?php
/**
 * Invalid target list exceptions
 */
class EmptyTargetListException extends Exception {};
class InvalidTargetException extends Exception {};
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
        $referenceIds = array();

        if ($target->check_source_type == Target::SOURCE_TYPE_CHECK_CATEGORIES) {
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

        $checkCount += TargetCheck::model()->with(array(
            "check" => array(
                "alias" => "tc",
                "joinType" => "LEFT JOIN"
            )
        ))->count($criteria);

        $criteria = new CDbCriteria();
        $criteria->addInCondition("check_control_id", $controlIds);

        if ($target->check_source_type == Target::SOURCE_TYPE_CHECK_CATEGORIES) {
            $criteria->addInCondition("reference_id", $referenceIds);
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
     * Reindex target's checks
     * @param Target $target
     * @throws Exception
     */
    public static function reindexTargetChecks(Target $target) {
        $admin = $target->project->admin ? $target->project->admin : User::getAdmin();
        $language = Language::model()->findByAttributes(array("default" => true));

        switch ($target->check_source_type) {
            case Target::SOURCE_TYPE_CHECK_CATEGORIES:
                $controlIds = array();
                $checkIds = array();
                $categoryIds = array();
                $targetCheckIds = array();

                $referenceIds = array();
                $references = TargetReference::model()->findAllByAttributes(array(
                    "target_id" => $target->id
                ));

                foreach ($references as $reference) {
                    $referenceIds[] = $reference->reference_id;
                }

                $targetCategories = TargetCheckCategory::model()->findAllByAttributes(array(
                    "target_id" => $target->id
                ));

                foreach ($targetCategories as $tc) {
                    $categoryIds[] = $tc->check_category_id;
                }

                $controls = CheckControl::model()->findAllByAttributes(array(
                    "check_category_id" => $categoryIds
                ));

                foreach ($controls as $control) {
                    $controlIds[] = $control->id;
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition("check_control_id", $controlIds);
                $criteria->addInCondition("reference_id", $referenceIds);

                $checks = Check::model()->findAll($criteria);

                foreach ($checks as $c) {
                    $checkIds[] = $c->id;
                }

                $targetChecks = TargetCheck::model()->findAllByAttributes(array(
                    "target_id" => $target->id
                ));

                foreach ($targetChecks as $tc) {
                    $targetCheckIds[] = $tc->check_id;
                }

                // clean target checks
                $criteria = new CDbCriteria();
                $criteria->addNotInCondition("check_id", $checkIds);
                $criteria->addColumnCondition(array(
                    "target_id" => $target->id
                ));
                TargetCheck::model()->deleteAll($criteria);

                $checksToAdd = Check::model()->findAllByAttributes(array(
                    "id" => array_values(array_diff($checkIds, $targetCheckIds))
                ));

                foreach ($checksToAdd as $check) {
                    $targetCheck = TargetCheckManager::create([
                        "target_id" => $target->id,
                        "check_id" => $check->id,
                        "user_id" => $admin->id,
                        "language_id" => $language->id,
                        "status" => TargetCheck::STATUS_OPEN,
                        "rating" => TargetCheck::RATING_NONE
                    ]);

                    foreach ($check->scripts as $script) {
                        $targetCheckScript = new TargetCheckScript();
                        $targetCheckScript->check_script_id = $script->id;
                        $targetCheckScript->target_check_id = $targetCheck->id;
                        $targetCheckScript->save();
                    }

                    foreach ($check->fields as $field) {
                        $targetCheckField = new TargetCheckField();
                        $targetCheckField->target_check_id = $targetCheck->id;
                        $targetCheckField->check_field_id = $field->id;
                        $targetCheckField->value = $field->getValue();
                        $targetCheckField->hidden = $field->hidden;
                        $targetCheckField->save();
                    }
                }

                break;

            case Target::SOURCE_TYPE_CHECKLIST_TEMPLATES:
                $checkIds = array();
                $targetCheckIds = array();
                $templateIds = array();

                foreach ($target->checklistTemplates as $template) {
                    $templateIds[] = $template->checklist_template_id;
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition("t.checklist_template_id", $templateIds);

                $templateChecks = ChecklistTemplateCheck::model()->with(array(
                    "check" => array(
                        "alias" => "tc",
                        "joinType" => "LEFT JOIN",
                    )
                ))->findAll($criteria);

                foreach ($templateChecks as $tc) {
                    $checkIds[] = $tc->check->id;
                }

                // clean target checks
                $criteria = new CDbCriteria();
                $criteria->addNotInCondition("check_id", $checkIds);
                $criteria->addColumnCondition(array(
                    "target_id" => $target->id
                ));
                TargetCheck::model()->deleteAll($criteria);

                $targetChecks = TargetCheck::model()->findAllByAttributes(array(
                    "target_id" => $target->id
                ));

                foreach ($targetChecks as $tc) {
                    $targetCheckIds[] = $tc->check_id;
                }

                foreach ($templateChecks as $check) {
                    if (in_array($check->check->id, $targetCheckIds) && !$target->canAddCheck($check->check->id)) {
                        continue;
                    }

                    $targetCheck = new TargetCheck();
                    $targetCheck->target_id = $target->id;
                    $targetCheck->check_id = $check->check->id;
                    $targetCheck->user_id = $admin;
                    $targetCheck->language_id = $language->id;
                    $targetCheck->status = TargetCheck::STATUS_OPEN;
                    $targetCheck->rating = TargetCheck::RATING_NONE;
                    $targetCheck->save();

                    foreach ($check->check->scripts as $script) {
                        $targetCheckScript = new TargetCheckScript();
                        $targetCheckScript->check_script_id = $script->id;
                        $targetCheckScript->target_check_id = $targetCheck->id;
                        $targetCheckScript->save();
                    }
                }

                break;

            default:
                throw new Exception("Unknown check source type.");
        }
    }

    /**
     * Returns target chain status
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public static function getChainStatus($id) {
        $target = Target::model()->findByPk($id);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        $key = JobManager::buildId(
            ChainJob::CHAIN_STATUS_TEMPLATE,
            array(
                "target_id" => $target->id
            )
        );
        $status = JobManager::getKeyValue($key);

        if (!in_array($status, array(Target::CHAIN_STATUS_STOPPED, Target::CHAIN_STATUS_IDLE, Target::CHAIN_STATUS_ACTIVE, Target::CHAIN_STATUS_INTERRUPTED))) {
            return null;
        }

        return $status;
    }

    /**
     * Set check chain status
     * @param $id
     * @param $status
     */
    public static function setChainStatus($id, $status) {
        $target = Target::model()->findByPk($id);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        if (!in_array($status, array(Target::CHAIN_STATUS_STOPPED, Target::CHAIN_STATUS_ACTIVE, Target::CHAIN_STATUS_IDLE, Target::CHAIN_STATUS_INTERRUPTED))) {
            throw new Exception("Unknown chain status.");
        }

        $key = JobManager::buildId(
            ChainJob::CHAIN_STATUS_TEMPLATE,
            array(
                "target_id" => $target->id
            )
        );

        JobManager::setKeyValue($key, $status);
    }

    /**
     * Returns last activated cell in target check chain
     * (when you resuming stopped chain)
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public static function getChainLastCellId($id) {
        $target = Target::model()->findByPk($id);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        $key = JobManager::buildId(
            ChainJob::CHAIN_CELL_ID_TEMPLATE,
            array(
                "target_id" => $target->id
            )
        );
        $activeCellId = JobManager::getKeyValue($key);

        if (!$activeCellId) {
            return null;
        }

        return $activeCellId;
    }

    /**
     * Set chain last cell id
     * @param $targetId
     * @param $cellId
     * @throws Exception
     */
    public static function setChainLastCellId($targetId, $cellId) {
        $target = Target::model()->findByPk($targetId);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        $key = JobManager::buildId(
            ChainJob::CHAIN_CELL_ID_TEMPLATE,
            array(
                "target_id" => $target->id
            )
        );

        JobManager::setKeyValue($key, $cellId);
    }

    /**
     * Delete key value of last check chain
     * @param $targetId
     * @throws Exception
     */
    public static function delChainLastCellId($targetId) {
        $target = Target::model()->findByPk($targetId);

        if (!$target) {
            throw new Exception("Target not found.");
        }

        $key = JobManager::buildId(
            ChainJob::CHAIN_CELL_ID_TEMPLATE,
            array(
                "target_id" => $target->id
            )
        );

        JobManager::delKey($key);
    }

    /**
     * Returns chain's messages
     * @param $id
     * @return array
     * @throws Exception
     */
    public static function getChainMessages() {
        // Redis doesn't support regexps, use glob
        $mask = JobManager::buildId(ChainJob::ID_TEMPLATE, array(
            "operation" => "*",
            "target_id" => "[0-9]*"
        ));
        $mask .= '.message';
        $keys = Resque::redis()->keys($mask);

        if (!is_array($keys)) {
            $keys = explode(" ", $keys);
        }

        $pattern = JobManager::buildId(ChainJob::ID_TEMPLATE, array(
            "operation" => sprintf("(%s|%s)", ChainJob::OPERATION_START, ChainJob::OPERATION_STOP),
            "target_id" => "(\d+)"
        ));
        $pattern = '/' . $pattern . '.message/';
        $messages = array();

        foreach ($keys as $key) {
            $key = str_replace("resque:", "", $key);
            preg_match_all($pattern, $key, $matches, PREG_PATTERN_ORDER);

            if (!empty($matches[0])) {
                $targetId = $matches[1][0];

                $messages[] = array(
                    "id" => $matches[1][0],
                    "status" => self::getChainStatus($targetId),
                    "message" => Resque::redis()->get($key)
                );
            }

            JobManager::delKey($key);
        }

        return $messages;
    }

    /**
     * Get chain checks
     * @param Target $target
     * @return array|CActiveRecord|mixed|null
     * @throws Exception
     */
    public static function getChainChecks(Target $target) {
        if (!$target->relations) {
            return [];
        }

        try {
            $relations = new SimpleXMLElement($target->relations, LIBXML_NOERROR);
        } catch (Exception $e) {
            throw new Exception("Invalid target relations.");
        }

        $checkIds = RelationManager::getCheckIds($relations);

        return TargetCheck::model()->findAllByAttributes([
            "check_id" => $checkIds,
            "target_id" => $target->id
        ]);
    }

    /**
     * Resolve hostname
     * @param Target $target
     * @throws Exception
     */
    public function resolveHost(Target $target) {
        $host = trim($target->host);
        $resolved = gethostbyname($host);

        if ($resolved != $host) {
            $target->ip = $resolved;
            $target->save();
        }
    }

    /**
     * Bind target to issue
     * @param Target $target
     * @param Issue $issue
     * @throws Exception
     */
    public function bindToIssue(Target $target, Issue $issue) {
        $check = $issue->check;
        $targetCheck = TargetCheck::model()->findByAttributes([
            "check_id" => $issue->check_id,
            "target_id" => $target->id
        ]);

        if (!$targetCheck) {
            if ($target->check_source_type == Target::SOURCE_TYPE_CHECKLIST_TEMPLATES) {
                $categoryIds = [];

                foreach ($target->targetChecks as $tc) {
                    $id = $tc->check->control->category_id;

                    if (!in_array($id, $categoryIds)) {
                        $categoryIds[] = $id;
                    }
                }

                foreach ($categoryIds as $id) {
                    $targetCheckCategory = TargetCheckCategory::model()->findByAttributes([
                        "check_category_id" => $id,
                        "target_id" => $target->id
                    ]);

                    if (!$targetCheckCategory) {
                        $targetCheckCategory = new TargetCheckCategory();
                        $targetCheckCategory->check_category_id = $id;
                        $targetCheckCategory->target_id = $target->id;
                        $targetCheckCategory->save();
                    }
                }

                $target->check_source_type = Target::SOURCE_TYPE_CHECK_CATEGORIES;
                $target->save();
            }

            $targetCheckCategory = TargetCheckCategory::model()->findByAttributes([
                "target_id" => $target->id,
                "check_category_id" => $issue->check->control->category_id
            ]);

            if (!$targetCheckCategory) {
                $targetCheckCategory = new TargetCheckCategory();
                $targetCheckCategory->check_category_id = $issue->check->control->category_id;
                $targetCheckCategory->target_id = $target->id;
                $targetCheckCategory->save();
            }

            $targetReference = TargetReference::model()->findByAttributes([
                "target_id" => $target->id,
                 "reference_id" => $check->reference_id
            ]);

            if (!$targetReference) {
                $targetReference = new TargetReference();
                $targetReference->target_id = $target->id;
                $targetReference->reference_id = $check->reference_id;
                $target->save();
            }

            $admin = $target->project->admin ? $target->project->admin : User::getAdmin();
            $language = System::model()->findByPk(1)->language;

            $targetCheck = TargetCheckManager::create([
                "target_id" => $target->id,
                "check_id" => $issue->check->id,
                "user_id" => $admin->id,
                "language_id" => $language->id,
                "status" => TargetCheck::STATUS_OPEN,
                "rating" => TargetCheck::RATING_NONE
            ]);

            foreach ($check->scripts as $script) {
                $targetCheckScript = new TargetCheckScript();
                $targetCheckScript->check_script_id = $script->id;
                $targetCheckScript->target_check_id = $targetCheck->id;
                $targetCheckScript->save();
            }

            foreach ($check->fields as $field) {
                $targetCheckField = new TargetCheckField();
                $targetCheckField->target_check_id = $targetCheck->id;
                $targetCheckField->check_field_id = $field->id;
                $targetCheckField->value = $field->getValue();
                $targetCheckField->hidden = $field->hidden;
                $targetCheckField->save();
            }
        }

        $evidence = new IssueEvidence();
        $evidence->issue_id = $issue->id;
        $evidence->target_check_id = $targetCheck->id;
        $evidence->save();

        ReindexJob::enqueue([
            "target_id" => $target->id
        ]);
    }

    /**
     * Filter targets
     * @param $query
     * @param array $exclude
     * @return array
     */
    public function filter($query, $exclude = []) {
        $escapedQuery = pg_escape_string($query);

        $criteria = new CDbCriteria();
        $criteria->order = "t.ip ASC";
        $criteria->addNotInCondition("t.id", $exclude);
        $criteria->order = "ipContains DESC, t.ip ASC";
        $criteria->select = "t.*, position(lower('$escapedQuery') in lower(t.ip))::boolean AS ipContains";

        if ($query) {
            $criteria->addSearchCondition("t.description", $query, true, "AND", "ILIKE");
            $criteria->addSearchCondition("t.ip", $query, true, "OR", "ILIKE");
        }

        if ($exclude) {
            $criteria->addNotInCondition("t.id", $exclude);
        }

        return Target::model()->findAll($criteria);
    }
}