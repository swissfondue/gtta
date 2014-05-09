<?php

/**
 * Check manager class
 */
class CheckManager {
    private $_catalogs = null;
    private $_languages = array();

    /**
     * Constructor
     * @param $catalogs
     */
    public function __construct($catalogs=null) {
        $this->_catalogs = $catalogs;

        foreach (Language::model()->findAll() as $language) {
            $this->_languages[$language->code] = $language->id;
        }
    }

    /**
     * Check if catalogs value has been correctly set
     */
    private function _checkCatalogs() {
        if (!$this->_catalogs) {
            throw new Exception("Catalogs value should be set.");
        }
    }

    /**
     * Get catalog category
     * @param $id
     * @return mixed
     * @throws Exception
     */
    private function _getCatalogCategory($id) {
        $this->_checkCatalogs();

        $cat = null;

        foreach ($this->_catalogs->categories as $category) {
            if ($category->id == $id) {
                $cat = $category;
                break;
            }
        }

        if ($cat == null) {
            throw new Exception("Category not found.");
        }

        return $cat;
    }

    /**
     * Get catalog control
     * @param $id
     * @return mixed
     * @throws Exception
     */
    private function _getCatalogControl($id) {
        $this->_checkCatalogs();

        $ctrl = null;

        foreach ($this->_catalogs->categories as $category) {
            foreach ($category->controls as $control) {
                if ($control->id == $id) {
                    $ctrl = $control;
                    break;
                }
            }

            if ($ctrl != null) {
                break;
            }
        }

        if ($ctrl == null) {
            throw new Exception("Control not found.");
        }

        return $ctrl;
    }

    /**
     * Get catalog reference
     * @param $id
     * @return mixed
     * @throws Exception
     */
    private function _getCatalogReference($id) {
        $this->_checkCatalogs();

        $ref = null;

        foreach ($this->_catalogs->references as $reference) {
            if ($reference->id == $id) {
                $ref = $reference;
                break;
            }
        }

        if ($ref == null) {
            throw new Exception("Reference not found.");
        }

        return $ref;
    }

    /**
     * Get category id
     * @param $externalId
     * @return CheckCategory
     */
    private function _getCategoryId($externalId) {
        $category = CheckCategory::model()->findByAttributes(array("external_id" => $externalId));

        if (!$category) {
            $data = $this->_getCatalogCategory($externalId);

            $category = new CheckCategory();
            $category->external_id = $externalId;
            $category->name = $data->name;
            $category->save();

            foreach ($data->l10n as $l10n) {
                $l = new CheckCategoryL10n();
                $l->language_id = $this->_languages[$l10n->code];
                $l->check_category_id = $category->id;
                $l->name = $l10n->name;
                $l->save();
            }
        }

        return $category->id;
    }

    /**
     * Get control id
     * @param $externalId
     * @return CheckControl
     */
    private function _getControlId($externalId) {
        $control = CheckControl::model()->findByAttributes(array("external_id" => $externalId));

        if (!$control) {
            $data = $this->_getCatalogControl($externalId);

            $control = new CheckControl();
            $control->check_category_id = $this->_getCategoryId($data->category_id);
            $control->external_id = $externalId;
            $control->name = $data->name;
            $control->sort_order = $data->sort_order;
            $control->save();

            foreach ($data->l10n as $l10n) {
                $l = new CheckControlL10n();
                $l->language_id = $this->_languages[$l10n->code];
                $l->check_control_id = $control->id;
                $l->name = $l10n->name;
                $l->save();
            }
        }

        return $control->id;
    }

    /**
     * Get reference id
     * @param $externalId
     * @return Reference
     */
    private function _getReferenceId($externalId) {
        $reference = Reference::model()->findByAttributes(array("external_id" => $externalId));

        if (!$reference) {
            $data = $this->_getCatalogReference($externalId);

            $reference = new Reference();
            $reference->external_id = $externalId;
            $reference->name = $data->name;
            $reference->url = $data->url;
            $reference->save();
        }

        return $reference->id;
    }

    /**
     * Create check
     * @param $check
     * @return Check
     */
    public function createCheck($check) {
        /** @var System $system */
        $system = System::model()->findByPk(1);
        $api = new CommunityApiClient($system->integration_key);
        $check = $api->getCheck($check)->check;

        if ($check->status == CommunityApiClient::STATUS_UNVERIFIED && !$system->community_allow_unverified) {
            throw new Exception("Installing unverified checks is prohibited.");
        }

        if ($system->community_min_rating > 0 && $check->rating < $system->community_min_rating) {
            throw new Exception("Check rating is below the system rating limit.");
        }

        $id = $check->id;
        $existingCheck = Check::model()->findByAttributes(array("external_id" => $id));

        if ($existingCheck) {
            return;
        }

        $control = $this->_getControlId($check->control_id);
        $reference = $this->_getReferenceId($check->reference_id);

        $c = new Check();
        $c->external_id = $check->id;
        $c->demo = false;
        $c->name = $check->name;
        $c->background_info = $check->background_info;
        $c->hints = $check->hints;
        $c->question = $check->question;
        $c->advanced = $check->advanced;
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

        foreach ($check->scripts as $script) {
            $pkg = Package::model()->findByAttributes(array(
                "external_id" => $script->package_id,
                "type" => Package::TYPE_SCRIPT,
                "status" => Package::STATUS_INSTALLED
            ));

            if (!$pkg) {
                $pkg = $pm->createPackage($script->package_id);
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
}
