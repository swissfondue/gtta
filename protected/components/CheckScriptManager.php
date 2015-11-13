<?php

/**
 * Check script manager
 */
class CheckScriptManager {
    /**
     * Add input from parsed package
     * @param CheckScript $script
     * @param $input
     * @param $order
     */
    private function _createInput(CheckScript $script, $input, $order) {
        // not validating data here, as PackageManager 100% returns all required fields
        $name = $input[PackageManager::SECTION_NAME];
        $type = $input[PackageManager::SECTION_TYPE];
        $description = $input[PackageManager::SECTION_DESCRIPTION];
        $visible = $input[PackageManager::SECTION_VISIBLE];
        $value = $input[PackageManager::SECTION_VALUE];

        $name = str_replace("_", " ", $name);
        $name = ucwords($name);
        $pm = new PackageManager();

        $object = new CheckInput();
        $object->check_script_id = $script->id;
        $object->type = $pm->getCheckInputType($type);
        $object->name = $name;
        $object->description = $description;
        $object->visible = $visible;
        $object->value = $value;
        $object->sort_order = $order;
        $object->save();

        $language = Language::model()->find("user_default");
        $l10n = new CheckInputL10n();
        $l10n->check_input_id = $object->id;
        $l10n->language_id = $language->id;
        $l10n->name = $object->name;
        $l10n->description = $object->description;
        $l10n->save();
    }

    /**
     * Create inputs for script
     * @param CheckScript $script
     */
    public function createInputs(CheckScript $script) {
        $pm = new PackageManager();
        $package = $pm->getData(Package::model()->findByPk($script->package_id));

        if (!isset($package[PackageManager::SECTION_INPUTS])) {
            return;
        }

        $order = 0;

        foreach ($package[PackageManager::SECTION_INPUTS] as $input) {
            $this->_createInput($script, $input, $order);
            $order++;
        }
    }
}