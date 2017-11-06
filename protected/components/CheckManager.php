<?php

/**
 * Check manager class
 */
class CheckManager {
    private $_languages = [];

    /**
     * Constructor
     */
    public function __construct() {
        foreach (Language::model()->findAll() as $language) {
            $this->_languages[$language->code] = $language->id;
        }
    }

    /**
     * Get control id
     * @param $externalId
     * @return CheckControl
     */
    private function _getControlId($externalId, $initial=false) {
        $cm = new ControlManager();
        $control = $cm->create($externalId, $initial);

        return $control->id;
    }

    /**
     * Get reference id
     * @param $externalId
     * @return Reference
     */
    private function _getReferenceId($externalId, $initial=false) {
        $rm = new ReferenceManager();
        $reference = $rm->create($externalId, $initial);

        return $reference->id;
    }

    /**
     * Get global field id
     * @param $externalId
     * @param bool $initial
     * @return int
     */
    private function _getFieldId($externalId, $initial=false) {
        $fm = new FieldManager();
        $field = $fm->create($externalId, $initial);

        return $field->id;
    }

    /**
     * Create check
     * @param $check
     * @return Check
     * @throws Exception
     */
    public function create($check, $initial=false) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $api = new CommunityApiClient($initial ? null : $system->integration_key);
        $check = $api->getCheck($check)->check;
        $control = $this->_getControlId($check->control_id, $initial);
        $reference = $this->_getReferenceId($check->reference_id, $initial);
        $c = null;

        try {
            if ($check->status == CommunityApiClient::STATUS_UNVERIFIED && !$system->community_allow_unverified) {
                throw new Exception(Yii::t("app", "Installing unverified checks is prohibited."));
            }

            if ($system->community_min_rating > 0 && $check->rating < $system->community_min_rating) {
                throw new Exception(Yii::t("app", "Check rating is below the system rating limit."));
            }

            $c = Check::model()->findByAttributes(["external_id" => $check->id]);

            if (!$c) {
                $c = new Check();
                $now = new DateTime();
                $c->create_time = $now->format(ISO_DATE_TIME);
            }

            $c->external_id = $check->id;
            $c->name = $check->name;
            $c->automated = $check->automated;
            $c->multiple_solutions = $check->multiple_solutions;
            $c->check_control_id = $control;
            $c->reference_id = $reference;
            $c->reference_code = $check->reference_code;
            $c->reference_url = $check->reference_url;
            $c->sort_order = $check->sort_order;
            $c->status = Check::STATUS_INSTALLED;
            $c->save();

            // l10n
            CheckL10n::model()->deleteAllByAttributes(["check_id" => $c->id]);

            foreach ($check->l10n as $l10n) {
                $l = new CheckL10n();
                $l->language_id = $this->_languages[$l10n->code];
                $l->check_id = $c->id;
                $l->name = $l10n->name;
                $l->save();
            }

            // results
            CheckResult::model()->deleteAllByAttributes(["check_id" => $c->id]);

            foreach ($check->results as $result) {
                $r = new CheckResult();
                $r->check_id = $c->id;
                $r->title = $result->title;
                $r->result = $result->result;
                $r->sort_order = $result->sort_order;
                $r->save();

                foreach ($result->l10n as $l10n) {
                    $l = new CheckResultL10n();
                    $l->language_id = $this->_languages[$l10n->code];
                    $l->check_result_id = $r->id;
                    $l->title = $l10n->title;
                    $l->result = $l10n->result;
                    $l->save();
                }
            }

            // solutions
            CheckSolution::model()->deleteAllByAttributes(["check_id" => $c->id]);

            foreach ($check->solutions as $solution) {
                $s = new CheckSolution();
                $s->check_id = $c->id;
                $s->title = $solution->title;
                $s->solution = $solution->solution;
                $s->sort_order = $solution->sort_order;
                $s->save();

                foreach ($solution->l10n as $l10n) {
                    $l = new CheckSolutionL10n();
                    $l->language_id = $this->_languages[$l10n->code];
                    $l->check_solution_id = $s->id;
                    $l->title = $l10n->title;
                    $l->solution = $l10n->solution;
                    $l->save();
                }
            }

            $pm = new PackageManager();

            // scripts
            CheckScript::model()->deleteAllByAttributes(["check_id" => $c->id]);

            foreach ($check->scripts as $script) {
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition([
                    "external_id" => $script->package_id,
                    "type" => Package::TYPE_SCRIPT,
                ]);
                $criteria->addInCondition("status", Package::getActiveStatuses());
                $pkg = Package::model()->find($criteria);

                if (!$pkg) {
                    try {
                        $pkg = $pm->create($script->package_id, $initial, true);
                    } catch (Exception $e) {
                        continue;
                    }
                }

                $s = new CheckScript();
                $s->check_id = $c->id;
                $s->package_id = $pkg->id;
                $s->save();

                foreach ($script->inputs as $input) {
                    $i = new CheckInput();
                    $i->check_script_id = $s->id;
                    $i->name = $input->name;
                    $i->type = $input->type;
                    $i->value = $input->value;
                    $i->description = $input->description;
                    $i->visible = $input->visible;
                    $i->sort_order = $input->sort_order;
                    $i->save();

                    foreach ($input->l10n as $l10n) {
                        $l = new CheckInputL10n();
                        $l->language_id = $this->_languages[$l10n->code];
                        $l->check_input_id = $i->id;
                        $l->name = $l10n->name;
                        $l->description = $l10n->description;
                        $l->save();
                    }
                }
            }

            $this->reindexFields($c);
            $c->refresh();

            foreach ($check->fields as $field) {
                $global = $this->_getFieldId($field->global_check_field_id, $initial);

                $f = CheckField::model()->findByAttributes([
                    "check_id" => $c->id,
                    "global_check_field_id" => $global
                ]);

                if (!$f) {
                    $f = new CheckField();
                    $f->check_id = $c->id;
                    $f->global_check_field_id = $global;
                }

                $f->hidden = $field->hidden;
                $f->value = $field->value;
                $f->save();
                $f->refresh();

                foreach ($this->_languages as $code => $languageId) {
                    $l10n = CheckFieldL10n::model()->findByAttributes([
                        "check_field_id" => $f->id,
                        "language_id" => $languageId
                    ]);

                    $value = null;

                    if (!$l10n) {
                        $l10n = new CheckFieldL10n();
                        $l10n->check_field_id = $f->id;
                        $l10n->language_id = $languageId;
                    }

                    foreach ($field->l10n as $fieldL10n) {
                        if ($fieldL10n->code != $code) {
                            continue;
                        }

                        $value = $fieldL10n->value;
                    }

                    $l10n->value = $value;
                    $l10n->save();
                }
            }
        } catch (Exception $e) {
            if (!$initial) {
                $api->installError([
                    "id" => $check->id,
                    "type" => "check",
                    "text" => $e->getMessage(),
                ]);
            }

            throw $e;
        }


        return $c;
    }

    /**
     * Get external ids
     * @return array
     */
    public function getExternalIds() {
        $checkIds = [];
        $checks = Check::model()->findAll("external_id IS NOT NULL AND status = :status", [
            "status" => Check::STATUS_INSTALLED
        ]);

        foreach ($checks as $check) {
            $checkIds[] = $check->external_id;
        }

        return $checkIds;
    }

    /**
     * Prepare check sharing
     * @param Check $check
     * @throws Exception
     */
    public function prepareSharing(Check $check) {
        if ($check->status != Check::STATUS_INSTALLED || $check->private) {
            return;
        }

        $pm = new PackageManager();

        if ($check->automated) {
            foreach ($check->scripts as $script) {
                $pm->share($script->package);
            }
        }

        $control = $check->control;
        $reference = $check->_reference;

        $fm = new FieldManager();

        foreach ($check->fields as $field) {
            $fm->share($field->global);
        }

        if (!$control->external_id) {
            $cm = new ControlManager();
            $cm->share($control);
        }

        if (!$reference->external_id) {
            $rm = new ReferenceManager();
            $rm->share($reference);
        }
    }

    /**
     * Serialize and share check
     * @param Check $check
     * @throws Exception
     */
    public function share(Check $check) {
        $this->prepareSharing($check);
        $system = System::model()->findByPk(1);

        $data = [
            "control_id" => $check->control->external_id,
            "reference_id" => $check->_reference->external_id,
            "reference_code" => $check->reference_code,
            "reference_url" => $check->reference_url,
            "name" => $check->name,
            "automated" => $check->automated,
            "multiple_solutions" => $check->multiple_solutions,
            "effort" => $check->effort,
            "sort_order" => $check->sort_order,
            "l10n" => [],
            "results" => [],
            "solutions" => [],
            "scripts" => [],
            "fields" => [],
        ];

        foreach ($check->l10n as $l10n) {
            $data["l10n"][] = [
                "code" => $l10n->language->code,
                "name" => $l10n->name,
            ];
        }

        foreach ($check->results as $result) {
            $r = [
                "title" => $result->title,
                "result" => $result->result,
                "sort_order" => $result->sort_order,
                "l10n" => [],
            ];

            foreach ($result->l10n as $l10n) {
                $r["l10n"][] = [
                    "code" => $l10n->language->code,
                    "title" => $l10n->title,
                    "result" => $l10n->result,
                ];
            }

            $data["results"][] = $r;
        }

        foreach ($check->solutions as $solution) {
            $s = [
                "title" => $solution->title,
                "solution" => $solution->solution,
                "sort_order" => $solution->sort_order,
                "l10n" => [],
            ];

            foreach ($solution->l10n as $l10n) {
                $s["l10n"][] = [
                    "code" => $l10n->language->code,
                    "title" => $l10n->title,
                    "solution" => $l10n->solution,
                ];
            }

            $data["solutions"][] = $s;
        }

        foreach ($check->scripts as $script) {
            if (!$script->package->external_id) {
                throw new Exception("Invalid package id.");
            }

            $s = [
                "package_id" => $script->package->external_id,
                "inputs" => [],
            ];

            foreach ($script->inputs as $input) {
                $i = [
                    "type" => $input->type,
                    "name" => $input->name,
                    "description" => $input->description,
                    "value" => $input->value,
                    "visible" => $input->visible,
                    "sort_order" => $input->sort_order,
                    "l10n" => [],
                ];

                foreach ($input->l10n as $l10n) {
                    $i["l10n"][] = [
                        "code" => $l10n->language->code,
                        "name" => $l10n->name,
                        "description" => $l10n->description,
                    ];
                }

                $s["inputs"][] = $i;
            }

            $data["scripts"][] = $s;
        }

        foreach ($check->fields as $field) {
            $f = [
                "global_check_field_id" => $field->global->external_id,
                "name" => $field->name,
                "value" => $field->value,
                "hidden" => $field->hiddenValue,
                "l10n" => [],
                "global" => null
            ];

            foreach ($field->l10n as $l10n) {
                $f["l10n"][] = [
                    "code" => $l10n->language->code,
                    "value" => $l10n->value,
                ];
            }

            $data["fields"][] = $f;
        }

        try {
            $api = new CommunityApiClient($system->integration_key);
            $check->external_id = $api->shareCheck(["check" => $data])->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $check->status = Check::STATUS_INSTALLED;
        $check->save();
    }

    /**
     * Reindex check fields
     * @param Check $check
     * @param array $globalFields
     * @throws Exception
     */
    public function reindexFields(Check $check, $globalFields = []) {
        if (!$globalFields) {
            $globalFields = GlobalCheckField::model()->findAll();
        }

        /** @var GlobalCheckField $gf */
        foreach ($globalFields as $gf) {
            $checkField = CheckField::model()->findByAttributes([
                "check_id" => $check->id,
                "global_check_field_id" => $gf->id
            ]);

            if (!$checkField) {
                $checkField = new CheckField();
                $checkField->global_check_field_id = $gf->id;
                $checkField->check_id = $check->id;
            }

            $checkField->value = $gf->value;
            $checkField->save();

            foreach ($gf->l10n as $l) {
                $l10n = CheckFieldL10n::model()->findByAttributes([
                    "check_field_id" => $checkField->id,
                    "language_id" => $l->language_id
                ]);

                if (!$l10n) {
                    $l10n = new CheckFieldL10n();
                    $l10n->check_field_id = $checkField->id;
                    $l10n->language_id = $l->language_id;
                }

                $l10n->value = $l->value;
                $l10n->save();
            }

            $tcm = new TargetCheckManager();
            $tcm->reindexFields($checkField);
        }
    }

    /**
     * Filter checks by string
     *
     * @param $query
     * @param $language
     * @param array $exclude
     *
     * @return array
     * @internal param $languageglobal_check_fields_l10n
     */
    public function filter($query, $language, $exclude=[]) {
        $criteria = new CDbCriteria();
        $fieldName = GlobalCheckField::FIELD_BACKGROUND_INFO;
        $criteria->join = "INNER JOIN check_fields cf ON cf.check_id = t.check_id ";
        $criteria->join .= "INNER JOIN checks c ON c.id = t.check_id ";
        $criteria->join .= "INNER JOIN check_controls cc ON cc.id = c.check_control_id ";
        $criteria->join .= "INNER JOIN check_controls_l10n ccl10n ON ccl10n.check_control_id = cc.id  AND ccl10n.language_id = t.language_id ";
        $criteria->join .= "INNER JOIN check_fields_l10n cfl10n ON cfl10n.check_field_id = cf.id AND cfl10n.language_id = t.language_id ";
        $criteria->join .= "INNER JOIN \"references\" ref ON ref.id = c.reference_id ";
        $criteria->join .= "INNER JOIN global_check_fields gcf ON gcf.name = '$fieldName' AND gcf.id = cf.global_check_field_id";

        if (!$query) {
            return [];
        }

        if (preg_match('/^(["\']).*\1$/m', $query)) {
            $query = trim($query, '"');
            $words = [$query];
        } else {
            $words = preg_split("/[ \-\.]+/", $query);
        }

        foreach ($words as $word) {
            $criteria->addSearchCondition("cc.name", $query, true, "OR", "ILIKE");
            $criteria->addSearchCondition("ccl10n.name", $query, true, "OR", "ILIKE");
            $criteria->addSearchCondition("cfl10n.value", $word, true, "OR", "ILIKE");
            $criteria->addSearchCondition("t.name", $word, true, "OR", "ILIKE");
            $criteria->addSearchCondition("c.reference_code", $word, true, "OR", "ILIKE");
            $criteria->addSearchCondition("ref.name", $word, true, "OR", "ILIKE");
        }

        $criteria->addColumnCondition(["t.language_id" => $language]);

        if ($exclude) {
            $criteria->addNotInCondition("t.check_id", $exclude);
        }

        return CheckL10n::model()->findAll($criteria);
    }
}
