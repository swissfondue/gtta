<?php

/**
 * Control manager class
 */
class ControlManager {
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
     * Prepare control sharing
     * @param CheckControl $control
     * @param bool $recursive
     * @throws Exception
     */
    public function prepareSharing(CheckControl $control, $recursive=false) {
        $category = $control->category;

        if (!$category->external_id) {
            $cm = new CategoryManager();
            $cm->prepareSharing($category);
        }

        if (!$control->external_id) {
            JobManager::enqueue(JobManager::JOB_COMMUNITY_SHARE, array(
                'type' => CommunityShareJob::TYPE_CONTROL,
                'obj_id' => $control->id,
            ));
        }

        if ($recursive) {
            $cm = new CheckManager();

            foreach ($control->checks as $check) {
                $cm->prepareSharing($check);
            }
        }
    }

    /**
     * Serialize and share control
     * @param CheckControl $control
     * @throws Exception
     */
    public function share(CheckControl $control) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $data = array(
            "category_id" => $control->category->external_id,
            "name" => $control->name,
            "sort_order" => $control->sort_order,
            "l10n" => array(),
        );

        foreach ($control->l10n as $l10n) {
            $data["l10n"][] = array(
                "code" => $l10n->language->code,
                "name" => $l10n->name,
            );
        }

        try {
            $api = new CommunityApiClient($system->integration_key);
            $control->external_id = $api->shareControl(array("control" => $data))->id;
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
        }

        $control->status = CheckControl::STATUS_INSTALLED;
        $control->save();
    }

    /**
     * Get category id
     * @param $externalId
     * @return CheckCategory
     */
    private function _getCategoryId($externalId) {
        $category = CheckCategory::model()->findByAttributes(array("external_id" => $externalId));

        if (!$category) {
            $cm = new CategoryManager();
            $category = $cm->create($externalId);
        }

        return $category->id;
    }

    /**
     * Create control
     * @param $control
     * @return CheckControl
     * @throws Exception
     */
    public function create($control) {
        /** @var System $system */
        $system = System::model()->findByPk(1);
        $api = new CommunityApiClient($system->integration_key);
        $control = $api->getControl($control)->control;

        $id = $control->id;
        $existingControl = CheckControl::model()->findByAttributes(array("external_id" => $id));

        if ($existingControl) {
            return $existingControl;
        }

        $category = $this->_getCategoryId($control->category_id);

        $c = new CheckControl();
        $c->check_category_id = $category;
        $c->external_id = $control->id;
        $c->sort_order = $control->sort_order;
        $c->name = $control->name;
        $c->status = CheckControl::STATUS_INSTALLED;
        $c->save();

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
