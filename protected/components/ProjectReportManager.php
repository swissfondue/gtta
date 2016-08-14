<?php

/**
 * Class ProjectReportManager
 */
class ProjectReportManager {
    /**
     * Prepare text for project report.
     */
    public function prepareText($text) {
        $text = preg_replace("~>\s*\n\s*<~", "><", $text);
        $text = str_replace(array("<br />", "<br/>"), "<br>", $text);
        $text = str_replace(array("\r", "\n", "\t"), " ", $text);
        $text = str_replace(array("\\r", "\\n", "\\t"), "", $text);

        $text = strip_tags($text, "<b><i><u><br><ol><ul><li>");
        $text = preg_replace("~<br>\s+~", "<br>", $text);
        $text = preg_replace("~</ul>\s+~", "</ul>", $text);
        $text = preg_replace("~</ol>\s+~", "</ol>", $text);
        $text = preg_replace("~</li>\s+~", "</li>", $text);

        $text = preg_replace("~<ul>[^<]+~", "</ul>", $text);
        $text = preg_replace("~<ol>[^<]+~", "</ul>", $text);
        $text = preg_replace("~</li>[^<]+~", "</li>", $text);
        $text = trim($text);

        return $text;
    }

    /**
     * Get rating for the given check distribution
     * @param $totalChecks
     * @param $lowRisk
     * @param $medRisk
     * @param $highRisk
     * @return float rating
     */
    private function _getRating($totalChecks, $lowRisk, $medRisk, $highRisk) {
        $medDamping = 1;
        $lowDamping = 1;
        $pedestal = 0;
        $range = 0;

        if ($totalChecks == 0) {
            return 0.0;
        }

        /** @var System $system */
        $system = System::model()->findByPk(1);

        if ($highRisk > 0) {
            $pedestal = $system->report_high_pedestal;
            $medDamping = $system->report_high_damping_med;
            $lowDamping = $system->report_high_damping_low;
            $range = $system->report_max_rating - $system->report_high_pedestal;
        } elseif ($medRisk > 0) {
            $pedestal = $system->report_med_pedestal;
            $lowDamping = $system->report_med_damping_low;
            $range = $system->report_high_pedestal - $system->report_med_pedestal;
        } elseif ($lowRisk > 0) {
            $pedestal = $system->report_low_pedestal;
            $range = $system->report_med_pedestal - $system->report_low_pedestal;
        }

        $highRisk = $highRisk / $totalChecks * $range;
        $medRisk = $medRisk / $totalChecks * ($range - $highRisk) * $medDamping;
        $lowRisk = $lowRisk / $totalChecks * ($range - $highRisk - $medRisk) * $lowDamping;
        $rating = $highRisk + $medRisk + $lowRisk + $pedestal;

        return $rating;
    }

    /**
     * Get rating for checks array
     * @param array $checks
     * @return float rating
     */
    private function _getChecksRating($checks) {
        $totalChecks = 0;
        $lowRisk = 0;
        $medRisk = 0;
        $highRisk = 0;

        foreach ($checks as $check) {
            $totalChecks++;

            switch ($check["rating"]) {
                case TargetCheck::RATING_NONE:
                case TargetCheck::RATING_HIDDEN:
                case TargetCheck::RATING_INFO:
                    break;

                case TargetCheck::RATING_LOW_RISK:
                    $lowRisk++;
                    break;

                case TargetCheck::RATING_MED_RISK:
                    $medRisk++;
                    break;

                case TargetChecK::RATING_HIGH_RISK:
                    $highRisk++;
                    break;
            }
        }

        return $this->_getRating($totalChecks, $lowRisk, $medRisk, $highRisk);
    }

    /**
     * Get category rating
     * @param $category
     * @return float rating
     */
    private function _getCategoryRating($category) {
        $checks = array();

        foreach ($category["controls"] as $control) {
            foreach ($control["checks"] as $check) {
                $checks[] = $check;
            }
        }

        return $this->_getChecksRating($checks);
    }

    /**
     * Get target rating
     * @param $target
     * @return float rating
     */
    private function _getTargetRating($target) {
        $checks = array();

        foreach ($target["categories"] as $category) {
            foreach ($category["controls"] as $control) {
                foreach ($control["checks"] as $check) {
                    $checks[] = $check;
                }
            }
        }

        return $this->_getChecksRating($checks);
    }

    /**
     * Get total rating
     * @param $targets
     * @return float rating
     */
    private function _getTotalRating($targets) {
        $checks = array();

        foreach ($targets as $target) {
            foreach ($target["categories"] as $category) {
                foreach ($category["controls"] as $control) {
                    foreach ($control["checks"] as $check) {
                        $checks[] = $check;
                    }
                }
            }
        }

        return $this->_getChecksRating($checks);
    }

    /**
     * Fulfillment report
     * @param Target[] $targets
     * @param int $language
     * @return array
     */
    private function _getFulfillmentReportData($targets, $language) {
        $data = [];

        foreach ($targets as $target) {
            $targetData = [
                "id" => $target->id,
                "host" => $target->hostPort,
                "description" => $target->description,
                "controls" => [],
            ];

            // get all references (they are the same across all target categories)
            $referenceIds = [];

            $references = TargetReference::model()->findAllByAttributes([
                "target_id" => $target->id
            ]);

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            // get all categories
            $categories = TargetCheckCategory::model()->with([
                "category" => [
                    "with" => [
                        "l10n" => [
                            "joinType" => "LEFT JOIN",
                            "on" => "language_id = :language_id",
                            "params" => ["language_id" => $language]
                        ]
                    ]
                ]
            ])->findAllByAttributes(
                ["target_id" => $target->id],
                ["order" => "COALESCE(l10n.name, category.name) ASC"]
            );

            foreach ($categories as $category) {
                // get all controls
                $controls = CheckControl::model()->with([
                    "l10n" => [
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => ["language_id" => $language],
                    ]
                ])->findAllByAttributes(
                    ["check_category_id" => $category->check_category_id],
                    ["order" => "t.sort_order ASC"]
                );

                if (!$controls) {
                    continue;
                }

                foreach ($controls as $control) {
                    $controlData = [
                        "name" => $category->category->localizedName . " / " . $control->localizedName,
                        "degree" => 0.0,
                    ];

                    $criteria = new CDbCriteria();

                    $criteria->addInCondition("t.reference_id", $referenceIds);
                    $criteria->addColumnCondition([
                        "t.check_control_id" => $control->id
                    ]);

                    $checks = Check::model()->with([
                        "targetChecks" => [
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status",
                            "params" => [
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                            ],
                        ],
                    ])->findAll($criteria);

                    if (!$checks) {
                        continue;
                    }

                    foreach ($checks as $check) {
                        foreach ($check->targetChecks as $tc) {
                            switch ($tc->rating) {
                                case TargetCheck::RATING_HIDDEN:
                                case TargetCheck::RATING_INFO:
                                    $controlData["degree"] += 0;
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $controlData["degree"] += 1;
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $controlData["degree"] += 2;
                                    break;

                                case TargetCheck::RATING_HIGH_RISK:
                                    $controlData["degree"] += 3;
                                    break;
                            }
                        }
                    }

                    $maxDegree = count($checks) * 3;
                    $controlData["degree"] = round(100 - $controlData["degree"] / $maxDegree * 100);
                    $targetData["controls"][] = $controlData;
                }
            }

            $data[] = $targetData;
        }

        return $data;
    }

    /**
     * Get weakest controls
     * @param Target[] $targets
     * @param int $language
     * @return array
     */
    public function _getWeakestControls($targets, $language) {
        $data = $this->_getFulfillmentReportData($targets, $language);

        $weakest = [];

        foreach ($data as $target) {
            $degree = 100.0;
            $weakest = null;

            foreach ($target["controls"] as $control) {
                if ($control["degree"] < $degree) {
                    $weakest = $control;
                    $degree = $control["degree"];
                }
            }

            $weakest[$target["id"]] = $weakest;
        }

        return $weakest;
    }

    /**
     * Get project report data
     * @param array $targetIds
     * @param array $templateCategoryIds
     * @param Project $project
     * @param $fields
     * @param $language
     * @return array
     */
    public function getProjectReportData($targetIds, $templateCategoryIds, $project, $fields, $language) {
        $criteria = new CDbCriteria();
        $criteria->addInCondition("id", $targetIds);
        $criteria->addColumnCondition(array("project_id" => $project->id));
        $criteria->order = "t.host ASC";
        $targets = Target::model()->with(array(
            "checkCount",
            "finishedCount",
            "infoCount",
            "lowRiskCount",
            "medRiskCount",
            "highRiskCount",
        ))->findAll($criteria);

        $data = array();
        $hasInfo = true;
        $hasSeparate = false;

        $totalRating = 0.0;
        $totalCheckCount = 0;
        $checksHigh = 0;
        $checksMed = 0;
        $checksLow = 0;
        $checksInfo = 0;
        $reportAttachments = array();

        $reducedChecks = array();
        $ratings = TargetCheck::getRatingNames();

        foreach ($targets as $target) {
            $targetData = array(
                "id" => $target->id,
                "host" => $target->hostPort,
                "description" => $target->description,
                "rating" => 0.0,
                "checkCount" => 0,
                "categories" => array(),
                "info" => 0,
                "separate" => array(),
                "separateCount" => 0,
            );

            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                "target_id" => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            // get all categories
            $categories = TargetCheckCategory::model()->with(array(
                "category" => array(
                    "with" => array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on"       => "language_id = :language_id",
                            "params"   => array( "language_id" => $language )
                        )
                    )
                )
            ))->findAllByAttributes(
                array("target_id" => $target->id ),
                array("order" => "COALESCE(l10n.name, category.name) ASC")
            );

            foreach ($categories as $category) {
                $categoryData = array(
                    "id"  => $category->check_category_id,
                    "name" => $category->category->localizedName,
                    "rating" => 0.0,
                    "checkCount" => 0,
                    "controls" => array(),
                    "info" => 0,
                    "separate" => 0,
                );

                // get all controls
                $controls = CheckControl::model()->with(array(
                    "customChecks" => array(
                        "alias" => "custom",
                        "on" => "custom.target_id = :target_id",
                        "params" => array("target_id" => $target->id),
                        "with" => "attachments",
                    ),
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findAllByAttributes(
                    array("check_category_id" => $category->check_category_id),
                    array("order" => "t.sort_order ASC")
                );

                if (!$controls)
                    continue;

                foreach ($controls as $control) {
                    $controlData = array(
                        "id" => $control->id,
                        "name" => $control->localizedName,
                        "rating" => 0.0,
                        "checkCount" => 0,
                        "checks" => array(),
                        "info" => 0,
                        "separate" => 0,
                    );

                    foreach ($control->customChecks as $check) {
                        $checkData = array(
                            "id" => $check->target_id . "-" . $check->check_control_id,
                            "custom" => true,
                            "name" => $check->name,
                            "background" => $this->prepareText($check->background_info),
                            "question" => $this->prepareText($check->question),
                            "result" => $check->result,
                            "tableResult" => null,
                            "rating" => $check->rating,
                            "ratingName" => $ratings[$check->rating],
                            "ratingColor" => "#999999",
                            "solutions" => array(),
                            "images" => array(),
                            "reference" => "CUSTOM",
                            "referenceUrl" => null,
                            "referenceCode" => "CHECK-" . $check->reference,
                            "referenceCodeUrl" => null,
                            "info" => $check->rating == TargetCheck::RATING_INFO,
                            "separate" => in_array($category->check_category_id, $templateCategoryIds),
                        );

                        if ($check->solution) {
                            $checkData["solutions"][] = $this->prepareText($check->solution);
                        }

                        if ($checkData["info"]) {
                            $controlData["info"]++;
                            $categoryData["info"]++;
                            $targetData["info"]++;
                            $hasInfo = true;
                        }

                        if ($checkData["separate"]) {
                            $controlData["separate"]++;
                            $categoryData["separate"]++;

                            if (!in_array($category->check_category_id, $targetData["separate"])) {
                                $targetData["separate"][] = $category->check_category_id;
                            }

                            $targetData["separateCount"]++;

                            $hasSeparate = true;
                        }

                        switch ($check->rating) {
                            case TargetCustomCheck::RATING_INFO:
                                $checkData["ratingColor"] = "#3A87AD";
                                $checkData["ratingValue"] = 0;
                                $checksInfo++;
                                break;

                            case TargetCustomCheck::RATING_LOW_RISK:
                                $checkData["ratingColor"] = "#53A254";
                                $checkData["ratingValue"] = 1;
                                $checksLow++;
                                break;

                            case TargetCustomCheck::RATING_MED_RISK:
                                $checkData["ratingColor"] = "#DACE2F";
                                $checkData["ratingValue"] = 2;
                                $checksMed++;
                                break;

                            case TargetCustomCheck::RATING_HIGH_RISK:
                                $checkData["ratingColor"] = "#D63515";
                                $checkData["ratingValue"] = 3;
                                $checksHigh++;
                                break;
                        }

                        if ($check->attachments) {
                            foreach ($check->attachments as $attachment) {
                                if (in_array($attachment->type, array("image/jpeg", "image/png", "image/gif", "image/pjpeg"))) {
                                    $checkData["images"][] = array(
                                        "title" => $attachment->title,
                                        "image" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path
                                    );
                                } else {
                                    $reportAttachments[] = array(
                                        "host" => $target->hostPort,
                                        "title" => $attachment->title,
                                        "check" => $check->name,
                                        "filename" => $attachment->name,
                                        "path" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path,
                                    );
                                }
                            }
                        }

                        if (in_array($check->rating, array(TargetCustomCheck::RATING_HIGH_RISK, TargetCustomCheck::RATING_MED_RISK, TargetCustomCheck::RATING_LOW_RISK))) {
                            $reducedChecks[] = array(
                                "target" => array(
                                    "id" => $target->id,
                                    "host" => $target->hostPort,
                                    "description" => $target->description
                                ),
                                "id" => $checkData["id"],
                                "name" => $checkData["name"],
                                "question" => $checkData["question"],
                                "solution" => $checkData["solutions"] ? implode("\n", $checkData["solutions"]) : "",
                                "rating" => $checkData["rating"],
                                "result" => $checkData["result"],
                                "ratingValue" => $checkData["ratingValue"],
                                "custom" => true,
                            );
                        }

                        $controlData["checks"][] = $checkData;
                    }

                    $criteria = new CDbCriteria();
                    $criteria->order = "t.sort_order ASC, tcs.id ASC";
                    $criteria->addInCondition("t.reference_id", $referenceIds);
                    $criteria->addColumnCondition(array(
                        "t.check_control_id" => $control->id
                    ));

                    $criteria->together = true;

                    $checks = Check::model()->with(array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => array("language_id" => $language)
                        ),
                        "targetChecks" => array(
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden",
                            "params" => array(
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                                "hidden" => TargetCheck::RATING_HIDDEN,
                            ),
                            "with" => array(
                                "solutions" => array(
                                    "alias" => "tss",
                                    "joinType" => "LEFT JOIN",
                                    "with" => array(
                                        "solution" => array(
                                            "alias" => "tss_s",
                                            "joinType" => "LEFT JOIN",
                                            "with" => array(
                                                "l10n" => array(
                                                    "alias" => "tss_s_l10n",
                                                    "on" => "tss_s_l10n.language_id = :language_id",
                                                    "params" => array("language_id" => $language)
                                                )
                                            )
                                        )
                                    )
                                ),
                                "attachments",
                            )
                        ),
                        "_reference"
                    ))->findAll($criteria);

                    foreach ($checks as $check) {
                        $ctr = 0;

                        foreach ($check->targetChecks as $tc) {
                            $checkFields = [];

                            foreach ($tc->getOrderedFields() as $f) {
                                if (
                                    !$f->getHidden() && (
                                        in_array($f->field->global->name, GlobalCheckField::$system) ||
                                        in_array($f->field->global->name, $fields)
                                    )
                                ) {
                                    $checkFields[] = [
                                        "name" => $f->field->global->name,
                                        "title" => $f->field->global->localizedTitle,
                                        "value" => $f->value
                                    ];
                                }
                            }

                            $checkData = array(
                                "id" => $check->id,
                                "custom" => false,
                                "name" => $check->localizedName . ($ctr > 0 ? " " . ($ctr + 1) : ""),
                                "fields" => $checkFields,
                                "tableResult" => $tc->table_result,
                                "rating" => 0,
                                "ratingName" => $ratings[$tc->rating],
                                "ratingColor" => "#999999",
                                "solutions" => array(),
                                "images" => array(),
                                "reference" => $check->_reference->name,
                                "referenceUrl" => $check->_reference->url,
                                "referenceCode" => $check->reference_code,
                                "referenceCodeUrl" => $check->reference_url,
                                "info" => $tc->rating == TargetCheck::RATING_INFO,
                                "separate" => in_array($category->check_category_id, $templateCategoryIds),
                            );

                            if ($tc->solution) {
                                $checkData["solutions"][] = $this->prepareText($tc->solution);
                            }

                            if ($checkData["info"]) {
                                $controlData["info"]++;
                                $categoryData["info"]++;
                                $targetData["info"]++;
                                $hasInfo = true;
                            }

                            if ($checkData["separate"]) {
                                $controlData["separate"]++;
                                $categoryData["separate"]++;

                                if (!in_array($category->check_category_id, $targetData["separate"])) {
                                    $targetData["separate"][] = $category->check_category_id;
                                }

                                $targetData["separateCount"]++;

                                $hasSeparate = true;
                            }

                            $checkData["rating"] = $tc->rating;

                            switch ($tc->rating) {
                                case TargetCheck::RATING_INFO:
                                    $checkData["ratingColor"] = "#3A87AD";
                                    $checkData["ratingValue"] = 0;
                                    $checksInfo++;
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $checkData["ratingColor"] = "#53A254";
                                    $checkData["ratingValue"] = 1;
                                    $checksLow++;
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $checkData["ratingColor"] = "#DACE2F";
                                    $checkData["ratingValue"] = 2;
                                    $checksMed++;
                                    break;

                                case TargetCheck::RATING_HIGH_RISK:
                                    $checkData["ratingColor"] = "#D63515";
                                    $checkData["ratingValue"] = 3;
                                    $checksHigh++;
                                    break;
                            }

                            if ($tc->solutions) {
                                foreach ($tc->solutions as $solution) {
                                    $checkData["solutions"][] = $this->prepareText($solution->solution->localizedSolution);
                                }
                            }

                            if ($tc->attachments) {
                                foreach ($tc->attachments as $attachment) {
                                    if (in_array($attachment->type, array("image/jpeg", "image/png", "image/gif", "image/pjpeg"))) {
                                        $checkData["images"][] = array(
                                            "title" => $attachment->title,
                                            "image" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path
                                        );
                                    } else {
                                        $reportAttachments[] = array(
                                            "host" => $target->hostPort,
                                            "title" => $attachment->title,
                                            "check" => $check->name,
                                            "filename" => $attachment->name,
                                            "path" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path,
                                        );
                                    }
                                }
                            }

                            if (in_array($tc->rating, array(TargetCheck::RATING_HIGH_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_LOW_RISK))) {
                                $question = "";
                                $result = "";

                                foreach ($checkData["fields"] as $f) {
                                    if ($f["name"] == GlobalCheckField::FIELD_QUESTION) {
                                        $question = $f["value"];
                                    }

                                    if ($f["name"] == GlobalCheckField::FIELD_RESULT) {
                                        $result = $f["value"];
                                    }
                                }

                                $reducedChecks[] = array(
                                    "target" => array(
                                        "id" => $target->id,
                                        "host" => $target->hostPort,
                                        "description" => $target->description
                                    ),
                                    "id" => $checkData["id"],
                                    "name" => $checkData["name"],
                                    "question" => $question,
                                    "solution" => $checkData["solutions"] ? implode("\n", $checkData["solutions"]) : "",
                                    "rating" => $checkData["rating"],
                                    "result" => $result,
                                    "ratingValue" => $checkData["ratingValue"],
                                );
                            }

                            $controlData["checks"][] = $checkData;
                            $ctr++;
                        }
                    }

                    $controlData["rating"] = $this->_getChecksRating($controlData["checks"]);
                    $controlData["checkCount"] = count($controlData["checks"]);
                    $categoryData["checkCount"] += count($controlData["checks"]);
                    $targetData["checkCount"] += count($controlData["checks"]);
                    $totalCheckCount += count($controlData["checks"]);

                    if ($controlData["checks"]) {
                        $categoryData["controls"][] = $controlData;
                    }
                }

                if ($categoryData["checkCount"]) {
                    $categoryData["rating"] = $this->_getCategoryRating($categoryData);

                    if ($categoryData["controls"]) {
                        $targetData["categories"][] = $categoryData;
                    }
                }
            }

            if ($targetData["checkCount"]) {
                $targetData["rating"] = $this->_getTargetRating($targetData);
            }

            $data[] = $targetData;
        }

        if ($totalCheckCount) {
            $totalRating = $this->_getTotalRating($data);
        }

        $data = array(
            "data" => $data,
            "targets" => $targets,
            "project" => $project,
            "rating" => $totalRating,
            "checks" => $totalCheckCount,
            "checksInfo" => $checksInfo,
            "checksLow" => $checksLow,
            "checksMed" => $checksMed,
            "checksHigh" => $checksHigh,
            "attachments" => $reportAttachments,
            "hasInfo" => $hasInfo,
            "hasSeparate" => $hasSeparate,
            "reducedChecks" => $reducedChecks,
            "weakestControls" => $this->_getWeakestControls($targets, $language),
        );

        return $data;
    }
}