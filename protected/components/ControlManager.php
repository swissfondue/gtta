<?php

/**
 * Control manager class
 */
class ControlManager {
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
     * Prepare control sharing
     * @param CheckControl $control
     * @throws Exception
     */
    public function prepareSharing(CheckControl $control) {
        $category = $control->category;

        if (!$category->external_id) {
            $cm = new CategoryManager();
            $cm->share($category);
        }
    }

    /**
     * Serialize and share control
     * @param CheckControl $control
     * @param bool $recursive
     * @throws Exception
     */
    public function share(CheckControl $control, $recursive=false) {
        $this->prepareSharing($control);
        $system = System::model()->findByPk(1);

        $data = [
            "category_id" => $control->category->external_id,
            "name" => $control->name,
            "sort_order" => $control->sort_order,
            "l10n" => [],
        ];

        foreach ($control->l10n as $l10n) {
            $data["l10n"][] = [
                "code" => $l10n->language->code,
                "name" => $l10n->name,
            ];
        }

        try {
            $api = new CommunityApiClient($system->integration_key);
            $control->external_id = $api->shareControl(["control" => $data])->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $control->status = CheckControl::STATUS_INSTALLED;
        $control->save();

        if ($recursive) {
            $cm = new CheckManager();

            foreach ($control->checks as $check) {
                $cm->share($check);
            }
        }
    }

    /**
     * Get category id
     * @param $externalId
     * @return CheckCategory
     */
    private function _getCategoryId($externalId, $initial) {
        $cm = new CategoryManager();
        $category = $cm->create($externalId, $initial);

        return $category->id;
    }

    /**
     * Create control
     * @param $control
     * @return CheckControl
     * @throws Exception
     */
    public function create($control, $initial) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $api = new CommunityApiClient($initial ? null : $system->integration_key);
        $control = $api->getControl($control)->control;
        $category = $this->_getCategoryId($control->category_id, $initial);
        $c = CheckControl::model()->findByAttributes(["external_id" => $control->id]);

        if (!$c) {
            $c = new CheckControl();
        }

        $c->check_category_id = $category;
        $c->external_id = $control->id;
        $c->sort_order = $control->sort_order;
        $c->name = $control->name;
        $c->status = CheckControl::STATUS_INSTALLED;
        $c->save();

        // l10n
        CheckControlL10n::model()->deleteAllByAttributes(["check_control_id" => $c->id]);

        foreach ($control->l10n as $l10n) {
            $l = new CheckControlL10n();
            $l->language_id = $this->_languages[$l10n->code];
            $l->check_control_id = $c->id;
            $l->name = $l10n->name;
            $l->save();
        }

        return $c;
    }
}
