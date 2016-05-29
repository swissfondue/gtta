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
        foreach ($checkField->check->targetChecks as $targetCheck) {
            $targetCheckField = TargetCheckField::model()->findAllByAttributes([
                "check_field_id" => $checkField->id,
                "target_check_id" => $targetCheck->id
            ]);

            // if readonly field -> update value
            if ($targetCheckField) {
                if ($checkField->global->type == GlobalCheckField::TYPE_WYSIWYG_READONLY) {
                    $targetCheckField->value = $checkField->value;
                    $targetCheckField->save();
                }
            } else {
                $tcf = new TargetCheckField();
                $tcf->target_check_id = $targetCheck->id;
                $tcf->check_field_id = $checkField->id;
                $tcf->value = $checkField->value;
            }
        }
    }
}
