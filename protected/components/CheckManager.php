<?php

/**
 * Check manager class
 */
class CheckManager {
    private $_languages = array();

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

            $c = Check::model()->findByAttributes(array("external_id" => $check->id));

            if (!$c) {
                $c = new Check();
                $now = new DateTime();
                $c->create_time = $now->format(ISO_DATE_TIME);
            }

            $c->external_id = $check->id;
            $c->name = $check->name;
            $c->background_info = $check->background_info;
            $c->hints = $check->hints;
            $c->question = $check->question;
            $c->automated = $check->automated;
            $c->multiple_solutions = $check->multiple_solutions;
            $c->protocol = $check->protocol;
            $c->port = $check->port;
            $c->check_control_id = $control;
            $c->reference_id = $reference;
            $c->reference_code = $check->reference_code;
            $c->reference_url = $check->reference_url;
            $c->sort_order = $check->sort_order;
            $c->status = Check::STATUS_INSTALLED;
            $c->save();

            // l10n
            CheckL10n::model()->deleteAllByAttributes(array("check_id" => $c->id));

            foreach ($check->l10n as $l10n) {
                $l = new CheckL10n();
                $l->language_id = $this->_languages[$l10n->code];
                $l->check_id = $c->id;
                $l->name = $l10n->name;
                $l->background_info = $l10n->background_info;
                $l->hints = $l10n->hints;
                $l->question = $l10n->question;
                $l->save();
            }

            // results
            CheckResult::model()->deleteAllByAttributes(array("check_id" => $c->id));

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
            CheckSolution::model()->deleteAllByAttributes(array("check_id" => $c->id));

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
            CheckScript::model()->deleteAllByAttributes(array("check_id" => $c->id));

            foreach ($check->scripts as $script) {
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array(
                    "external_id" => $script->package_id,
                    "type" => Package::TYPE_SCRIPT,
                ));
                $criteria->addInCondition("status", Package::getActiveStatuses());
                $pkg = Package::model()->find($criteria);

                if (!$pkg) {
                    $pkg = $pm->create($script->package_id, $initial, true);
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
        } catch (Exception $e) {
            if (!$initial) {
                $api->installError(array(
                    "id" => $check->id,
                    "type" => "check",
                    "text" => $e->getMessage(),
                ));
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
        $checkIds = array();
        $checks = Check::model()->findAll("external_id IS NOT NULL AND status = :status", array(
            "status" => Check::STATUS_INSTALLED
        ));

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
                $pm->prepareSharing($script->package);
            }
        }

        $control = $check->control;
        $reference = $check->_reference;

        if (!$control->external_id) {
            $cm = new ControlManager();
            $cm->prepareSharing($control);
        }

        if (!$reference->external_id) {
            $rm = new ReferenceManager();
            $rm->prepareSharing($reference);
        }

        if (!$check->external_id) {
            CommunityShareJob::enqueue(array(
                "type" => CommunityShareJob::TYPE_CHECK,
                "obj_id" => $check->id,
            ));
        }
    }

    /**
     * Serialize and share check
     * @param Check $check
     * @throws Exception
     */
    public function share(Check $check) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $data = array(
            "control_id" => $check->control->external_id,
            "reference_id" => $check->_reference->external_id,
            "reference_code" => $check->reference_code,
            "reference_url" => $check->reference_url,
            "name" => $check->name,
            "background_info" => $check->background_info,
            "hints" => $check->hints,
            "question" => $check->question,
            "automated" => $check->automated,
            "multiple_solutions" => $check->multiple_solutions,
            "protocol" => $check->protocol,
            "port" => $check->port,
            "sort_order" => $check->sort_order,
            "l10n" => array(),
            "results" => array(),
            "solutions" => array(),
            "scripts" => array()
        );

        foreach ($check->l10n as $l10n) {
            $data["l10n"][] = array(
                "code" => $l10n->language->code,
                "name" => $l10n->name,
                "background_info" => $l10n->background_info,
                "hints" => $l10n->hints,
                "question" => $l10n->question,
            );
        }

        foreach ($check->results as $result) {
            $r = array(
                "title" => $result->title,
                "result" => $result->result,
                "sort_order" => $result->sort_order,
                "l10n" => array(),
            );

            foreach ($result->l10n as $l10n) {
                $r["l10n"][] = array(
                    "code" => $l10n->language->code,
                    "title" => $l10n->title,
                    "result" => $l10n->result,
                );
            }

            $data["results"][] = $r;
        }

        foreach ($check->solutions as $solution) {
            $s = array(
                "title" => $solution->title,
                "solution" => $solution->solution,
                "sort_order" => $solution->sort_order,
                "l10n" => array(),
            );

            foreach ($solution->l10n as $l10n) {
                $s["l10n"][] = array(
                    "code" => $l10n->language->code,
                    "title" => $l10n->title,
                    "solution" => $l10n->solution,
                );
            }

            $data["solutions"][] = $s;
        }

        foreach ($check->scripts as $script) {
            if (!$script->package->external_id) {
                throw new Exception("Invalid package id.");
            }

            $s = array(
                "package_id" => $script->package->external_id,
                "inputs" => array(),
            );

            foreach ($script->inputs as $input) {
                $i = array(
                    "type" => $input->type,
                    "name" => $input->name,
                    "description" => $input->description,
                    "value" => $input->value,
                    "visible" => $input->visible,
                    "sort_order" => $input->sort_order,
                    "l10n" => array(),
                );

                foreach ($input->l10n as $l10n) {
                    $i["l10n"][] = array(
                        "code" => $l10n->language->code,
                        "name" => $l10n->name,
                        "description" => $l10n->description,
                    );
                }

                $s["inputs"][] = $i;
            }

            $data["scripts"][] = $s;
        }

        try {
            $api = new CommunityApiClient($system->integration_key);
            $check->external_id = $api->shareCheck(array("check" => $data))->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $check->status = Check::STATUS_INSTALLED;
        $check->save();
    }
}
