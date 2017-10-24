<?php

/**
 * Category manager class
 */
class CategoryManager {
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
     * Serialize and share category
     * @param CheckCategory $category
     * @param $recursive
     * @throws Exception
     */
    public function share(CheckCategory $category, $recursive=false) {
        $system = System::model()->findByPk(1);

        $data = [
            "name" => $category->name,
            "l10n" => [],
        ];

        foreach ($category->l10n as $l10n) {
            $data["l10n"][] = [
                "code" => $l10n->language->code,
                "name" => $l10n->name,
            ];
        }

        try {
            $api = new CommunityApiClient($system->integration_key);
            $category->external_id = $api->shareCategory(array("category" => $data))->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $category->status = CheckCategory::STATUS_INSTALLED;
        $category->save();

        if ($recursive) {
            $cm = new ControlManager();

            foreach ($category->controls as $control) {
                $cm->share($control, $recursive);
            }
        }
    }

    /**
     * Create category
     * @param $category
     * @return CheckCategory
     * @throws Exception
     */
    public function create($category, $initial=false) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $api = new CommunityApiClient($initial ? null : $system->integration_key);
        $category = $api->getCategory($category)->category;
        $c = CheckCategory::model()->findByAttributes(["external_id" => $category->id]);

        if (!$c) {
            $c = new CheckCategory();
        }

        $c->external_id = $category->id;
        $c->name = $category->name;
        $c->status = CheckCategory::STATUS_INSTALLED;
        $c->save();

        // l10n
        CheckCategoryL10n::model()->deleteAllByAttributes(["check_category_id" => $c->id]);

        foreach ($category->l10n as $l10n) {
            $l = new CheckCategoryL10n();
            $l->language_id = $this->_languages[$l10n->code];
            $l->check_category_id = $c->id;
            $l->name = $l10n->name;
            $l->save();
        }

        return $c;
    }

    /**
     * Filter categories by string
     *
     * @param $query
     * @param $language
     *
     * @return array
     */
    public function filter($query, $language) {
        $escapedQuery = pg_escape_string($query);

        $criteria = new CDbCriteria();
        $criteria->order = "nameContains DESC, t.name ASC";
        $criteria->select = "t.*, position(lower('$escapedQuery') in lower(t.name))::boolean AS nameContains";

        if ($query) {
            $criteria->addSearchCondition("t.name", $query, true, "AND", "ILIKE");
        }

        $criteria->addColumnCondition(["t.language_id" => $language], "OR");

        return CheckCategoryL10n::model()->findAll($criteria);
    }
}
