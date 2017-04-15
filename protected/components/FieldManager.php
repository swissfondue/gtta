<?php

/**
 * Class FieldManager
 */
class FieldManager {
    /**
     * Languages array
     * @var array
     */
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
     * Validate field value
     * @param $type
     * @param $value
     * @return bool
     * @throws Exception
     */
    public static function validateField($type, $value) {
        switch ($type) {
            case GlobalCheckField::TYPE_CHECKBOX:
            case GlobalCheckField::TYPE_WYSIWYG_READONLY:
            case GlobalCheckField::TYPE_TEXTAREA:
            case GlobalCheckField::TYPE_TEXT:
                return true;
            case GlobalCheckField::TYPE_RADIO:
                $values = json_decode($value, true);

                if ($values === null) {
                    return false;
                }

                foreach ($values as $v) {
                    if (is_array($v)) {
                        return false;
                    }
                }

                return true;

            default:
                throw new Exception("Invalid field type.");
        }
    }

    /**
     * Share field
     * @param GlobalCheckField $field
     * @throws Exception
     */
    public function share(GlobalCheckField $field) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $data = [
            "type" => $field->type,
            "name" => $field->name,
            "title" => $field->title,
            "value" => $field->value,
            "sort_order" => $field->sort_order,
            "hidden" => $field->hidden,
            "l10n" => []
        ];

        foreach ($field->l10n as $l10n) {
            $data["l10n"][] = [
                "code" => $l10n->language->code,
                "title" => $l10n->title,
                "value" => $l10n->value
            ];
        }

        try {
            $api = new CommunityApiClient($system->integration_key);
            $field->external_id = $api->shareField(["field" => $data])->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $field->save();
    }

    /**
     * Create global check field object
     * @param $field
     * @param $initial
     * @return array|GlobalCheckField|mixed|null
     */
    public function create($field, $initial) {
        $system = System::model()->findByPk(1);

        $api = new CommunityApiClient($initial ? null : $system->integration_key);
        $field = $api->getField($field)->field;

        $f = GlobalCheckField::model()->findByAttributes(["external_id" => $field->id]);

        if (!$f) {
            $f = GlobalCheckField::model()->findByAttributes(["name" => $field->name]);
        }

        if (!$f) {
            $f = new GlobalCheckField();
        }

        $f->external_id = $field->id;
        $f->type = $field->type;
        $f->name = $field->name;
        $f->title = $field->title;
        $f->sort_order = $field->sort_order;
        $f->hidden = $field->hidden;
        $f->value = $field->value;
        $f->save();

        // l10n
        GlobalCheckFieldL10n::model()->deleteAllByAttributes(["global_check_field_id" => $f->id]);

        foreach ($this->_languages as $code => $id) {
            $l = new GlobalCheckFieldL10n();
            $l->language_id = $id;
            $l->global_check_field_id = $f->id;

            $title = null;
            $value = null;

            foreach ($field->l10n as $l10n) {
                if ($l10n->code == $code) {
                    $title = $l10n->title;
                    $value = $l10n->value;
                    break;
                }
            }

            $l->title = $title;
            $l->value = $value;
            $l->save();
        }

        return $f;
    }
}
