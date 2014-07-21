<?php

/**
 * Category manager class
 */
class CategoryManager {
    /**
     * Prepare category sharing
     * @param CheckCategory $category
     * @param bool $recursive
     * @throws Exception
     */
    public function prepareSharing(CheckCategory $category, $recursive=false) {
        if (!$category->external_id) {
            $category->status = CheckCategory::STATUS_SHARE;
            $category->save();
        }

        if ($recursive) {
            $cm = new ControlManager();

            foreach ($category->controls as $control) {
                $cm->prepareSharing($control, $recursive);
            }
        }
    }

    /**
     * Serialize and share category
     * @param CheckCategory $category
     * @throws Exception
     */
    public function share(CheckCategory $category) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $data = array(
            "name" => $category->name,
            "l10n" => array(),
        );

        foreach ($category->l10n as $l10n) {
            $data["l10n"][] = array(
                "code" => $l10n->language->code,
                "name" => $l10n->name,
            );
        }

        $api = new CommunityApiClient($system->integration_key);
        $category->external_id = $api->shareCategory(array("category" => $data))->id;
        $category->status = CheckCategory::STATUS_INSTALLED;
        $category->save();
    }
}
