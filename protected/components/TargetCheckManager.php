<?php
/**
 * Class TargetCheckManager
 */
class TargetCheckManager {
    /**
     * Target check create
     * @param Check $check
     * @param $data
     * @return TargetCheck
     * @throws Exception
     */
    public function create(Check $check, $data) {
        $targetCheck = new TargetCheck();

        try {
            $targetCheck->target_id = $data["target_id"];
            $targetCheck->check_id = $check->id;
            $targetCheck->user_id = $data["user_id"];
            $targetCheck->language_id = $data["language_id"];
            $targetCheck->status = isset($data["status"]) ? $data["status"] : TargetCheck::STATUS_OPEN;
            $targetCheck->rating = isset($data["rating"]) ? $data["rating"] : TargetCheck::RATING_NONE;

            $targetCheck->save();
            $targetCheck->refresh();

            if (isset($data["result"])) {
                $targetCheck->setFieldValue(
                    GlobalCheckField::FIELD_RESULT,
                    $data["result"]
                );
            }

            if (isset($data["poc"])) {
                $targetCheck->setFieldValue(
                    GlobalCheckField::FIELD_POC,
                    $data["poc"]
                );
            }

            if (isset($data["solutions"]) && is_array($data["solutions"])) {
                $ids = $data["solutions"];

                if (!$check->multiple_solutions) {
                    $ids = array_slice($ids, 0, 1);
                }

                $solutions = CheckSolution::model()->findAllByPk($ids);

                foreach ($solutions as $solution) {
                    $targetCheckSolution = new TargetCheckSolution();
                    $targetCheckSolution->check_solution_id = $solution->id;
                    $targetCheckSolution->target_check_id = $targetCheck->id;
                    $targetCheckSolution->save();
                }
            }

            foreach ($check->scripts as $script) {
                $targetCheckScript = new TargetCheckScript();
                $targetCheckScript->check_script_id = $script->id;
                $targetCheckScript->target_check_id = $targetCheck->id;
                $targetCheckScript->save();
            }

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if (!$language) {
                $language = Language::model()->findByAttributes(array(
                    "default" => true
                ));
            }


            /** @var CheckField $field */
            foreach ($check->fields as $field) {
                $this->createField($targetCheck, $field, $language);
            }

            if (!$targetCheck->getCategory()) {
                $targetCheckCategory = new TargetCheckCategory();
                $targetCheckCategory->target_id = $targetCheck->target_id;
                $targetCheckCategory->check_category_id = $check->control->check_category_id;
                $targetCheckCategory->save();
            }

            $reference = TargetReference::model()->findByAttributes([
                "target_id" => $targetCheck->target_id,
                "reference_id" => $check->reference_id
            ]);

            if (!$reference) {
                $reference = new TargetReference();
                $reference->reference_id = $check->reference_id;
                $reference->target_id = $targetCheck->target_id;
                $reference->save();
            }
        } catch (Exception $e) {
            throw new Exception("Can't create check.");
        }

        return $targetCheck;
    }

    /**
     * Create target check field
     * @param TargetCheck $tc
     * @param CheckField $field
     * @param Language|null $language
     * @return TargetCheckField
     */
    public function createField(TargetCheck $tc, CheckField $field, Language $language = null) {

        $exists = TargetCheckField::model()->findByAttributes([
            "target_check_id" => $tc->id,
            "check_field_id" => $field->id
        ]);

        if ($exists) {
            return $exists;
        }

        $targetCheckField = new TargetCheckField();
        $targetCheckField->target_check_id = $tc->id;
        $targetCheckField->check_field_id = $field->id;

        // radio contains JSON
        if ($field->getType() != GlobalCheckField::TYPE_RADIO) {
            $targetCheckField->value = $field->getValue($language);
        }

        $targetCheckField->hidden = $field->hidden;
        $targetCheckField->save();

        return $targetCheckField;
    }

    /**
     * Start check
     * @param $id
     * @throws Exception
     */
    public static function start($id, $chain=false) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        $now = new DateTime();

        $params = [
            "operation" => AutomationJob::OPERATION_START,
            "obj_id" => $targetCheck->id,
            "started" => $now->format(ISO_DATE_TIME),
        ];

        if ($chain) {
            $params["chain"] = true;
        }

        AutomationJob::enqueue($params);
    }

    /**
     * Stop check
     * @param $id
     * @throws Exception
     */
    public static function stop($id) {
        $id = (int) $id;
        $targetCheck = TargetCheck::model()->findByPk($id);

        if (!$targetCheck) {
            throw new Exception("Check not found.");
        }

        if ($targetCheck->isRunning) {
            AutomationJob::enqueue(array(
                "operation" => AutomationJob::OPERATION_STOP,
                "obj_id" => $targetCheck->id,
            ));
        }
    }

    /**
     * Get running check ids
     * @param $id
     * @return array
     * @throws Exception
     */
    public static function getRunning() {
        $mask = JobManager::buildId(AutomationJob::ID_TEMPLATE, array(
            "operation" => "*",
            "obj_id" => "*",
        ));
        $mask .= ".pid";
        $keys = Resque::redis()->keys($mask);

        if (!is_array($keys)) {
            $keys = explode(" ", $keys);
        }

        $ids = array();

        foreach ($keys as $key) {
            if (preg_match("/check.start.\d+/", $key, $match)) {
                preg_match("/\d+/", $match[0], $id);
                $ids[] = $id[0];
            }
        }

        return $ids;
    }

    /**
     * Get check started time
     * @param $id
     * @return mixed
     */
    public static function getStartTime($id) {
        $job = JobManager::buildId(AutomationJob::ID_TEMPLATE, array(
            "operation" => AutomationJob::OPERATION_START,
            "obj_id" => $id,
        ));

        return JobManager::getVar($job, "started");
    }

    /**
     * Reindex target check fields
     * @param CheckField $checkField
     * @throws Exception
     */
    public function reindexFields(CheckField $checkField) {
        foreach ($checkField->check->targetChecks as $targetCheck) {
            $tcf = TargetCheckField::model()->findByAttributes([
                "check_field_id" => $checkField->id,
                "target_check_id" => $targetCheck->id
            ]);

            // if empty field -> update value
            if (!$tcf) {
                $tcf = $this->createField($targetCheck, $checkField);
            }

            if (!$tcf->value && $checkField->getType() != GlobalCheckField::TYPE_RADIO) {
                $tcf->value = $checkField->getValue();
            }

            $tcf->save();
        }
    }

    /**	
     * Get check human readable data
     * @param TargetCheck $tc
     * @return array
     */
    public static function getData(TargetCheck $tc) {
        $renderController = new CController("RenderController");

        $attachmentList = array();
        $attachments = TargetCheckAttachment::model()->findAllByAttributes(array(
            "target_check_id" => $tc->id
        ));

        foreach ($attachments as $attachment) {
            $attachmentList[] = array(
                "name" => CHtml::encode($attachment->name),
                "path" => $attachment->path,
                "url" => Yii::app()->createUrl('project/attachment', array('path' => $attachment->path)),
            );
        }

        $table = null;

        if ($tc->table_result) {
            $table = new ResultTable();
            $table->parse($tc->table_result);
        }

        $time = TargetCheckManager::getStartTime($tc->id);
        $startedText = null;

        if ($time) {
            $started = new DateTime($time);
            $time = time() - strtotime($time);
            $user = $tc->user;

            if ($tc->status != TargetCheck::STATUS_FINISHED) {
                $startedText = Yii::t("app", "Started by {user} on {date} at {time}", array(
                    "{user}" => $user->name ? $user->name : $user->email,
                    "{date}" => $started->format("d.m.Y"),
                    "{time}" => $started->format("H:i:s"),
                ));
            }
        } else {
            $time = -1;
        }

        return [
            "id" => $tc->id,
            "overrideTarget" => $tc->overrideTarget,
            "result" => $tc->result,
            "poc" => $tc->poc,
            "tableResult" => $table ? $renderController->renderPartial("/project/target/check/tableresult", array("table" => $table, "check" => $tc), true) : "",
            "finished" => !$tc->isRunning,
            "time" => $time,
            "attachmentControlUrl" => Yii::app()->createUrl("project/controlattachment"),
            "attachments" => $attachmentList,
            "startedText" => $startedText,
        ];
    }

    /**
     * Add target check evidence
     * @param TargetCheck $targetCheck
     * @return IssueEvidence
     * @throws Exception
     */
    public function addEvidence(TargetCheck $targetCheck) {
        $target = $targetCheck->target;

        $issue = Issue::model()->findByAttributes([
            "project_id" => $target->project_id,
            "check_id" => $targetCheck->check_id,
        ]);

        if (!$issue) {
            $pm = new ProjectManager();
            $issue = $pm->addIssue($target->project, $targetCheck->check);
        }

        $evidence = IssueEvidence::model()->findByAttributes([
            "issue_id" => $issue->id,
            "target_check_id" => $targetCheck->id
        ]);

        if ($evidence) {
            return $evidence;
        }

        $evidence = new IssueEvidence();
        $evidence->issue_id = $issue->id;
        $evidence->target_check_id = $targetCheck->id;
        $evidence->save();

        return $evidence;
    }
}