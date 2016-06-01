<?php

/**
 * Class FieldManager
 */
class FieldManager {
    /**
     * Reindex check fields
     * @param Check $check
     * @throws Exception
     */
    public static function reindexCheckFields(GlobalCheckField $field) {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition([
            "gf.id" => $field->id
        ]);
        $checks = Check::model()->with([
            "fields" => [
                "alias" => "f",
                "with" => [
                    "global" => ["alias" => "gf"]
                ]
            ],
        ])->findAll($criteria);

        $checkIds = [];

        foreach ($checks as $c) {
            $checkIds[] = $c->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addNotInCondition("id", $checkIds);
        $noFieldChecks = Check::model()->findAll($criteria);

        $languages = Language::model()->findAll();

        foreach ($noFieldChecks as $check) {
            $cf = new CheckField();
            $cf->check_id = $check->id;
            $cf->global_check_field_id = $field->id;
            $cf->save();

            foreach ($languages as $l) {
                $cfl10n = new CheckFieldL10n();
                $cfl10n->check_field_id = $cf->id;
                $cfl10n->language_id = $l->id;
                $cfl10n->save();
            }
        }
    }

    /**
     * Reindex target check fields
     * @param CheckField $checkField
     */
    public static function reindexTargetCheckFields(CheckField $checkField) {
        $language = Language::model()->findByAttributes([
            "user_default" => true
        ]);

        foreach ($checkField->check->targetChecks as $targetCheck) {
            $targetCheckField = TargetCheckField::model()->findByAttributes([
                "check_field_id" => $checkField->id,
                "target_check_id" => $targetCheck->id
            ]);

            // if readonly field -> update value
            if ($targetCheckField) {
                if ($checkField->global->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) {
                    $targetCheckField->value = $checkField->getValue($language->id);
                    $targetCheckField->save();
                }
            } else {
                $tcf = new TargetCheckField();
                $tcf->target_check_id = $targetCheck->id;
                $tcf->check_field_id = $checkField->id;
                $tcf->value = $checkField->getValue($language->id);
                $tcf->save();
            }
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
}
