<?php

/**
 * Control manager class
 */
class ControlManager {
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
            $control->status = CheckControl::STATUS_SHARE;
            $control->save();
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

        $api = new CommunityApiClient($system->integration_key);
        $control->external_id = $api->shareControl(array("control" => $data))->id;
        $control->status = CheckControl::STATUS_INSTALLED;
        $control->save();
    }
}
