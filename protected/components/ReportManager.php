<?php

/**
 * Class ProjectReportManager
 */
class ReportManager {
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
                "host" => $target->getHostPort(),
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
            $w = null;

            foreach ($target["controls"] as $control) {
                if ($control["degree"] < $degree) {
                    $w = $control;
                    $degree = $control["degree"];
                }
            }


            $weakest[$target["id"]] = $w;
        }

        return $weakest;
    }

    /**
     * Get check data
     * @param Target $target
     * @param Check $check
     * @param TargetCheck $tc
     * @param array $fields
     * @return array
     */
    private function _getTargetCheckData(Target $target, Check $check, TargetCheck $tc, $fields) {
        $checkFields = [];
        $ratings = TargetCheck::getRatingNames();

        /** @var TargetCheckField $f */
        foreach ($tc->getOrderedFields() as $f) {
            if (!$f->getHidden() && in_array($f->field->global->name, $fields)) {
                $checkFields[] = [
                    "name" => $f->field->global->name,
                    "title" => $f->field->global->localizedTitle,
                    "value" => $f->value
                ];
            }
        }

        $checkData = array(
            "id" => $check->id,
            "targetCheckId" => $tc->id,
            "custom" => false,
            "name" => $check->localizedName,
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
            "control" => $check->control->name,
            "category" => $check->control->category->name
        );

        if ($tc->solution) {
            $checkData["solutions"][] = $this->prepareText($tc->solution);
        }

        $checkData["rating"] = $tc->rating;

        switch ($tc->rating) {
            case TargetCheck::RATING_INFO:
                $checkData["ratingColor"] = "#3A87AD";
                $checkData["ratingValue"] = 0;
                break;

            case TargetCheck::RATING_LOW_RISK:
                $checkData["ratingColor"] = "#53A254";
                $checkData["ratingValue"] = 1;
                break;

            case TargetCheck::RATING_MED_RISK:
                $checkData["ratingColor"] = "#DACE2F";
                $checkData["ratingValue"] = 2;
                break;

            case TargetCheck::RATING_HIGH_RISK:
                $checkData["ratingColor"] = "#D63515";
                $checkData["ratingValue"] = 3;
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
                        "host" => $target->getHostPort(),
                        "title" => $attachment->title,
                        "check" => $check->name,
                        "filename" => $attachment->name,
                        "path" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path,
                    );
                }
            }
        }

        return $checkData;
    }

    /**
     * Get issue data
     * @param array $targetIds
     * @param Project $project
     * @param array $fields
     * @param $language
     * @return array
     */
    public function getIssueData($targetIds, $project, $fields, $language) {
        $data = [];
        $reducedData = [];

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(["t.project_id" => $project->id]);
        $criteria->addInCondition("tc.target_id", $targetIds);
        $criteria->together = true;

        $issues = Issue::model()
            ->with([
                "check" => [
                    "with" => [
                        "l10n" => [
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => ["language_id" => $language],
                        ],
                        "_reference"
                    ]
                ],
                "evidences" => [
                    "alias" => "e",
                    "with" => [
                        "targetCheck" => [
                            "alias" => "tc",
                            "joinType" => "LEFT JOIN",
                            "on" => "e.target_check_id = tc.id AND tc.status = :status AND tc.rating != :hidden",
                            "params" => [
                                "status" => TargetCheck::STATUS_FINISHED,
                                "hidden" => TargetCheck::RATING_HIDDEN,
                            ],
                            "with" => [
                                "solutions" => [
                                    "alias" => "tss",
                                    "joinType" => "LEFT JOIN",
                                    "with" => [
                                        "solution" => [
                                            "alias" => "tss_s",
                                            "joinType" => "LEFT JOIN",
                                            "with" => [
                                                "l10n" => [
                                                    "alias" => "tss_s_l10n",
                                                    "on" => "tss_s_l10n.language_id = :language_id",
                                                    "params" => ["language_id" => $language],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                "attachments",
                                "target",
                            ]
                        ]
                    ]
                ]
            ])
            ->findAll($criteria);

        /** @var Issue $issue */
        foreach ($issues as $issue) {
            $check = $issue->check;

            $issueData = [
                "id" => $issue->id,
                "name" => $check->getLocalizedName(),
                "reference" => $check->_reference->name,
                "referenceUrl" => $check->_reference->url,
                "referenceCode" => $check->reference_code,
                "referenceCodeUrl" => $check->reference_url,
                "backgroundInfo" => $check->getBackgroundInfo(),
                "question" => $check->getQuestion(),
                "hints" => $check->getHints(),
                "evidences" => [],
                "topRating" => 0,
            ];

            $evidences = [];

            /** @var IssueEvidence $evidence */
            foreach ($issue->evidences as $evidence) {
                $tc = $evidence->targetCheck;
                $target = $tc->target->getHostPort();

                if (!isset($evidences[$target])) {
                    $evidences[$target] = [];
                }

                $evidenceData = $this->_getTargetCheckData($tc->target, $check, $tc, $fields);
                $evidenceData["target"] = $target;
                $evidences[$target][] = $evidenceData;

                if (in_array($tc->rating, [
                    TargetCheck::RATING_HIGH_RISK,
                    TargetCheck::RATING_MED_RISK,
                    TargetCheck::RATING_LOW_RISK
                ])) {
                    if (!isset($reducedData[$issue->id])) {
                        $reducedData[$issue->id] = $issueData;

                        if (!isset($reducedData[$issue->id]["evidences"][$target])) {
                            $reducedData[$issue->id]["evidences"][$target] = [];
                        }
                    }

                    $result = null;

                    foreach ($evidenceData["fields"] as $f) {
                        if ($f["name"] == GlobalCheckField::FIELD_RESULT) {
                            $result = $f["value"];
                        }
                    }

                    $reducedData[$issue->id]["evidences"][$target][] = array_merge($evidenceData, [
                        "result" => $result,
                        "solution" => $evidenceData["solutions"] ? implode("\n", $evidenceData["solutions"]) : "",
                    ]);
                }
            }

            $issueData["evidences"] = $evidences;

            if (!$evidences) {
                continue;
            }

            $data[] = $issueData;
        }

        return [
            "issues" => $data,
            "reducedIssues" => $reducedData,
        ];
    }

    /**
     * Get project report data
     * @param array $targetIds
     * @param array $templateCategoryIds
     * @param Project $project
     * @param $fields
     * @param $language
     * @param $uniqueId
     * @return array
     */
    public function getProjectReportData($targetIds, $templateCategoryIds, $project, $fields, $language, $uniqueId=false) {
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
                "host" => $target->getHostPort(),
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

                if (!$controls) {
                    continue;
                }

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
                            "targetCheckId" => $check->id,
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
                            "control" => $control->name,
                            "category" => $control->category->name
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
                                        "host" => $target->getHostPort(),
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
                                    "host" => $target->getHostPort(),
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
                                "targetCheckId" => $uniqueId ? $checkData["targetCheckId"]: null,
                                "control" => $checkData["control"],
                                "category" => $checkData["category"],
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

                        /** @var TargetCheck $tc */
                        foreach ($check->targetChecks as $tc) {
                            $checkData = $this->_getTargetCheckData($target, $check, $tc, $fields);
                            $checkData["name"] .= ($ctr > 0 ? " " . ($ctr + 1) : "");
                            $checkData["separate"] = in_array($category->check_category_id, $templateCategoryIds);

                            switch ($tc->rating) {
                                case TargetCheck::RATING_INFO:
                                    $checksInfo++;
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $checksLow++;
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $checksMed++;
                                    break;

                                case TargetCheck::RATING_HIGH_RISK:
                                    $checksHigh++;
                                    break;
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

                            if (in_array($tc->rating, array(TargetCheck::RATING_HIGH_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_LOW_RISK))) {
                                $question = "";
                                $result = "";
                                $technicalSolution = "";
                                $managementSolution = "";
                                $technicalResult = "";
                                $managementResult = "";

                                foreach ($checkData["fields"] as $f) {
                                    if ($f["name"] == GlobalCheckField::FIELD_QUESTION) {
                                        $question = $f["value"];
                                    }

                                    if ($f["name"] == GlobalCheckField::FIELD_RESULT) {
                                        $result = $f["value"];
                                    }

                                    if ($f["name"] == GlobalCheckField::FIELD_TECHNICAL_SOLUTION) {
                                        $technicalSolution = $f["value"];
                                    }

                                    if ($f["name"] == GlobalCheckField::FIELD_MANAGEMENT_SOLUTION) {
                                        $managementSolution = $f["value"];
                                    }

                                    if ($f["name"] == GlobalCheckField::FIELD_TECHNICAL_RESULT) {
                                        $technicalResult = $f["value"];
                                    }

                                    if ($f["name"] == GlobalCheckField::FIELD_MANAGEMENT_RESULT) {
                                        $managementResult = $f["value"];
                                    }
                                }

                                $reducedChecks[] = array(
                                    "target" => array(
                                        "id" => $target->id,
                                        "host" => $target->getHostPort(),
                                        "description" => $target->description
                                    ),
                                    "id" => $checkData["id"],
                                    "name" => $checkData["name"],
                                    "question" => $question,
                                    "solution" => $checkData["solutions"] ? implode("\n", $checkData["solutions"]) : "",
                                    "rating" => $checkData["rating"],
                                    "result" => $result,
                                    "ratingValue" => $checkData["ratingValue"],
                                    "technicalSolution" => $technicalSolution,
                                    "managementSolution" => $managementSolution,
                                    "technicalResult" => $technicalResult,
                                    "managementResult" => $managementResult,
                                    "targetCheckId" => $uniqueId ? $checkData["targetCheckId"]: null,
                                    "control" => $checkData["control"],
                                    "category" => $checkData["category"],
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

        $data = array_merge($this->getIssueData($targetIds, $project, $fields, $language), [
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
        ]);

        return $data;
    }

    /**
     * Get fulfillment report data
     * @param array $targets
     * @param $language
     * @return array
     */
    public function getFulfillmentReportData($targets, $language) {
        $data = array();

        foreach ($targets as $target) {
            $targetData = array(
                "id"          => $target->id,
                "host"        => $target->getHostPort(),
                "description" => $target->description,
                "controls"    => array(),
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
                            "on" => "language_id = :language_id",
                            "params" => array("language_id" => $language)
                        )
                    )
                )
            ))->findAllByAttributes(
                array("target_id" => $target->id),
                array("order" => "COALESCE(l10n.name, category.name) ASC")
            );

            foreach ($categories as $category) {
                // get all controls
                $controls = CheckControl::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findAllByAttributes(
                    array("check_category_id" => $category->check_category_id),
                    array("order" => "t.sort_order ASC")
                );

                if (!$controls) {
                    continue;
                }

                foreach ($controls as $control) {
                    $controlData = array(
                        "name"  => $category->category->localizedName . " / " . $control->localizedName,
                        "degree" => 0.0,
                    );

                    $criteria = new CDbCriteria();

                    $criteria->addInCondition("t.reference_id", $referenceIds);
                    $criteria->addColumnCondition(array(
                        "t.check_control_id" => $control->id
                    ));

                    $checks = Check::model()->with(array(
                        "targetChecks" => array(
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status",
                            "params" => array(
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                            ),
                        ),
                    ))->findAll($criteria);

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
     * Risk matrix data
     * @param array $targets
     * @param array $matrix
     * @param array $risks
     * @param int $language
     * @return array
     */
    public function getRiskMatrixData($targets, $matrix, $risks, $language) {
        $data = array();

        foreach ($targets as $target) {
            $mtrx = array();
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                "target_id" => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            foreach ($target->_categories as $category) {
                $controlIds = array();

                $controls = CheckControl::model()->findAllByAttributes(array(
                    "check_category_id" => $category->check_category_id
                ));

                foreach ($controls as $control) {
                    $controlIds[] = $control->id;
                }

                $criteria = new CDbCriteria();
                $criteria->order = "t.sort_order ASC";
                $criteria->addInCondition("t.reference_id", $referenceIds);
                $criteria->addInCondition("t.check_control_id", $controlIds);
                $criteria->together = true;

                $checks = Check::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "l10n.language_id = :language_id",
                        "params" => array( "language_id" => $language )
                    ),
                    "targetChecks" => array(
                        "alias" => "tcs",
                        "joinType" => "INNER JOIN",
                        "on" => "tcs.target_id = :target_id AND tcs.status = :status AND (tcs.rating = :high OR tcs.rating = :med)",
                        "params" => array(
                            "target_id" => $target->id,
                            "status" => TargetCheck::STATUS_FINISHED,
                            "high" => TargetCheck::RATING_HIGH_RISK,
                            "med" => TargetCheck::RATING_MED_RISK,
                        ),
                    )
                ))->findAll($criteria);

                foreach ($checks as $check) {
                    if (!isset($matrix[$target->id][$check->id])) {
                        continue;
                    }

                    $ctr = 0;

                    foreach ($risks as $riskId => $risk) {
                        $ctr++;

                        if (!isset($matrix[$target->id][$check->id][$risk->id])) {
                            continue;
                        }

                        $riskName = "R" . $ctr;

                        $damage = $matrix[$target->id][$check->id][$risk->id]["damage"] - 1;
                        $likelihood = $matrix[$target->id][$check->id][$risk->id]["likelihood"] - 1;

                        if (!isset($mtrx[$damage])) {
                            $mtrx[$damage] = array();
                        }

                        if (!isset($mtrx[$damage][$likelihood])) {
                            $mtrx[$damage][$likelihood] = array();
                        }

                        if (!in_array($riskName, $mtrx[$damage][$likelihood])) {
                            $mtrx[$damage][$likelihood][] = $riskName;
                        }
                    }
                }
            }

            $data[] = array(
                "host" => $target->getHostPort(),
                "description" => $target->description,
                "matrix" => $mtrx
            );
        }

        return $data;
    }

    /**
     * Sort checks by ratings
     */
    public static function sortChecksByRating($a, $b) {
        if ($a["ratingValue"] == $b["ratingValue"]) {
            return 0;
        }

        return $a["ratingValue"] < $b["ratingValue"] ? 1 : -1;
    }

    /**
     * Sort controls.
     */
    public static function sortControls($a, $b) {
        return $a["degree"] > $b["degree"];
    }

     /**
     * Get rating for the given check distribution
     * @param $totalChecks
     * @param $lowRisk
     * @param $medRisk
     * @param $highRisk
     * @return float rating
     */
    public function getRating($totalChecks, $lowRisk, $medRisk, $highRisk) {
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
    public function getChecksRating($checks) {
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

        return $this->getRating($totalChecks, $lowRisk, $medRisk, $highRisk);
    }

    /**
     * Generate project function.
     * @param ProjectReportForm $form
     */
    public function generateProjectReport($form) {

    }

    /**
     * Comparison report
     * @param Project $project1
     * @param Project $project2
     * @return array
     */
    public function getComparisonReportData($project1, $project2) {
        $targets1 = Target::model()->findAllByAttributes(
            array("project_id" => $project1->id),
            array("order" => "t.host ASC")
        );

        $targets2 = Target::model()->findAllByAttributes(
            array("project_id" => $project2->id),
            array("order" => "t.host ASC")
        );

        // find corresponding targets
        $data = array();

        foreach ($targets1 as $target1) {
            foreach ($targets2 as $target2) {
                if ($target2->getHostPort() == $target1->getHostPort()) {
                    $data[] = array(
                        $target1,
                        $target2
                    );

                    break;
                }
            }
        }

        if (!$data) {
            return [];
        }

        $targetsData = [];

        foreach ($data as $targets) {
            $targetData = array(
                'host' => $targets[0]->getHostPort(),
                'ratings' => array()
            );

            foreach ($targets as $target) {
                $rating = 0;
                $checkCount = 0;

                // get all references (they are the same across all target categories)
                $referenceIds = array();

                $references = TargetReference::model()->findAllByAttributes(array(
                    'target_id' => $target->id
                ));

                foreach ($references as $reference) {
                    $referenceIds[] = $reference->reference_id;
                }

                $checksData = array();

                foreach ($target->_categories as $category) {
                    $controls = CheckControl::model()->with(array(
                        "customChecks" => array(
                            "alias" => "custom",
                            "on" => "custom.target_id = :target_id",
                            "params" => array("target_id" => $target->id)
                        ),
                    ))->findAllByAttributes(array(
                        "check_category_id" => $category->check_category_id
                    ));

                    $controlIds = array();

                    foreach ($controls as $control) {
                        $controlIds[] = $control->id;

                        foreach ($control->customChecks as $custom) {
                            $checksData[] = array("rating" => $custom->rating);
                        }
                    }

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("reference_id", $referenceIds);
                    $criteria->addInCondition("check_control_id", $controlIds);

                    $checks = Check::model()->with(array(
                        "targetChecks" => array(
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden",
                            "params" => array(
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                                "hidden" => TargetCheck::RATING_HIDDEN,
                            ),
                        ),
                    ))->findAll($criteria);

                    if (!$checks) {
                        continue;
                    }

                    foreach ($checks as $check) {
                        foreach ($check->targetChecks as $tc) {
                            $checksData[] = array(
                                "rating" => $tc->rating
                            );
                        }
                    }
                }

                $targetData["ratings"][] = $this->_getChecksRating($checksData);
            }

            $targetsData[] = $targetData;
        }

        return $targetsData;
    }

    /**
     * Prepare text for vulnerabilities report.
     */
    private function _prepareVulnExportText($text) {
        $text = str_replace(array("\r", "\n"), '', $text);
        $text = str_replace(array("<br>", "<br/>", "<br />"), "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');

        return $text;
    }

    /**
     * Vulnerability export
     * @param Target[] $targets
     * @param int $language
     * @param array $columns
     * @param array $ratings
     * @return array
     */
    private function _getVulnExportData($targets, $language, $columns, $ratings) {
        if (!$targets) {
            return [];
        }

        $data = array();

        $statuses = array(
            TargetCheck::STATUS_VULN_OPEN => Yii::t("app", "Open"),
            TargetCheck::STATUS_VULN_RESOLVED => Yii::t("app", "Resolved"),
        );

        foreach ($targets as $target) {
            // get all references (they are the same across all target categories)
            $referenceIds = [];

            $references = TargetReference::model()->findAllByAttributes(array(
                "target_id" => $target->id
            ));

            if (!$references) {
                continue;
            }

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            // get all categories
            $categories = TargetCheckCategory::model()->with("category")->findAllByAttributes(
                ["target_id" => $target->id],
                ["order" => "category.name ASC"]
            );

            if (!$categories) {
                continue;
            }

            foreach ($categories as $category) {
                // get all controls
                $controls = CheckControl::model()->findAllByAttributes(
                    ["check_category_id" => $category->check_category_id],
                    ["order" => "t.sort_order ASC"]
                );

                if (!$controls) {
                    continue;
                }

                foreach ($controls as $control) {
                    $criteria = new CDbCriteria();
                    $criteria->order = "t.sort_order ASC, tcs.id ASC";
                    $criteria->addInCondition("t.reference_id", $referenceIds);
                    $criteria->addColumnCondition(array(
                        "t.check_control_id" => $control->id
                    ));
                    $criteria->together = true;

                    $checks = Check::model()->with([
                        "l10n" => [
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => ["language_id" => $language]
                        ],
                        "targetChecks" => [
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden",
                            "params" => [
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                                "hidden" => TargetCheck::RATING_HIDDEN,
                            ],
                            "with" => [
                                "solutions" => [
                                    "alias" => "tss",
                                    "joinType" => "LEFT JOIN",
                                    "with" => [
                                        "solution" => [
                                            "alias" => "tss_s",
                                            "joinType" => "LEFT JOIN",
                                            "with" => [
                                                "l10n" => [
                                                    "alias" => "tss_s_l10n",
                                                    "on" => "tss_s_l10n.language_id = :language_id",
                                                    "params" => ["language_id" => $language]
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        "_reference"
                    ])->findAll($criteria);

                    $criteria = new CDbCriteria();
                    $criteria->addCondition("target_id = :target_id");
                    $criteria->addCondition("rating != :hidden");
                    $criteria->addCondition("check_control_id = :check_control_id");
                    $criteria->params = [
                        "target_id" => $target->id,
                        "hidden" => TargetCustomCheck::RATING_HIDDEN,
                        "check_control_id" => $control->id
                    ];

                    $customChecks = TargetCustomCheck::model()->findAll($criteria);

                    if (!$checks && !$customChecks) {
                        continue;
                    }

                    foreach ($checks as $check) {
                        $ctr = 0;

                        foreach ($check->targetChecks as $tc) {
                            $row = $this->_getVulnExportRow([
                                "type" => "check",
                                "check" => $tc,
                                "target" => $target,
                                "ctr" => $ctr,
                                "columns" => $columns,
                                "ratings" => $ratings,
                                "statuses" => $statuses,
                            ]);

                            if (!$row) {
                                continue;
                            }

                            $data[] = $row;
                            $ctr++;
                        }
                    }

                    foreach ($customChecks as $cc) {
                        $row = $this->_getVulnExportRow([
                            "type" => "custom",
                            "check" => $cc,
                            "target" => $target,
                            "columns" => $columns,
                            "ratings" => $ratings,
                            "statuses" => $statuses,
                        ]);

                        if (!$row) {
                            continue;
                        }

                        $data[] = $row;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Returns vuln export row data
     * @param $data
     * @return null
     */
    private function _getVulnExportRow($data) {
        $type = $data["type"];
        $check = $data["check"];
        $target = $data["target"];
        $ratings = $data["ratings"];
        $statuses = $data["statuses"];
        $columns = $data["columns"];
        $ctr = null;

        $ratingNames = TargetCheck::getRatingNames();;

        if ($type == TargetCheck::TYPE) {
            $ctr = $data['ctr'];
        }

        if (!in_array($check->rating, $ratings)) {
            return null;
        }

        $row = array();

        if (in_array(TargetCheck::COLUMN_TARGET, $columns)) {
            $row[TargetCheck::COLUMN_TARGET] = $target->getHostPort();
        }

        if (in_array(TargetCheck::COLUMN_NAME, $columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_NAME] = $check->check->localizedName . ($ctr > 0 ? " " . ($ctr + 1) : "");
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_NAME] = $check->name ? $check->name : 'CUSTOM-CHECK-' . $check->reference;
            }
        }

        if (in_array(TargetCheck::COLUMN_REFERENCE, $columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_REFERENCE] = $check->check->_reference->name .
                    ($check->check->reference_code ? '-' . $check->check->reference_code : '');
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_REFERENCE] = "CUSTOM-CHECK-" . $check->reference;
            }
        }


        if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_BACKGROUND_INFO] = $this->_prepareVulnExportText($check->check->backgroundInfo);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_BACKGROUND_INFO] = $check->background_info;
            }
        }

        if (in_array(TargetCheck::COLUMN_QUESTION, $columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_QUESTION] = $this->_prepareVulnExportText($check->check->question);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_QUESTION] = $check->question;
            }
        }

        if (in_array(TargetCheck::COLUMN_RESULT, $columns)) {
            $row[TargetCheck::COLUMN_RESULT] = $check->result;
        }
        
        if (in_array(TargetCheck::COLUMN_SOLUTION, $columns)) {
            if ($type == TargetCheck::TYPE) {
                $solutions = array();

                foreach ($check->solutions as $solution) {
                    $solutions[] = $this->_prepareVulnExportText($solution->solution->localizedSolution);
                }

                if ($check->solution) {
                    $solutions[] = $this->_prepareVulnExportText($check->solution);
                }

                $row[TargetCheck::COLUMN_SOLUTION] = implode("\n", $solutions);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_SOLUTION] = $check->solution;
            }
        }

        if (in_array(TargetCheck::COLUMN_ASSIGNED_USER, $columns)) {
            $user = $check->vulnUser ? $check->vulnUser : null;

            if ($user) {
                $row[TargetCheck::COLUMN_ASSIGNED_USER] = $user->name ? $user->name : $user->email;
            } else {
                $row[TargetCheck::COLUMN_ASSIGNED_USER] = '';
            }
        }

        if (in_array(TargetCheck::COLUMN_RATING, $columns)) {
            $row[TargetCheck::COLUMN_RATING] = $ratingNames[$check->rating];
        }

        if (in_array(TargetCheck::COLUMN_STATUS, $columns)) {
            $row[TargetCheck::COLUMN_STATUS] =
                $statuses[$check->vuln_status ? $check->vuln_status : TargetCheck::STATUS_VULN_OPEN];
        }

        return $row;
    }

    /**
     * Generate vulnerability export report
     * @param Project $project
     * @param $targets
     * @param $language
     * @param $hdr
     * @param $columns
     * @param $ratings
     * @throws CException
     * @throws Exception
     */
    public function generateVulnExportReport(Project $project, $targets, $language, $hdr, $columns, $ratings) {
        $data = $this->_getVulnExportData($targets, $language, $columns, $ratings);

        if (!$data) {
            return;
        }

        $header = array();

        if ($hdr) {
            if (in_array(TargetCheck::COLUMN_TARGET, $columns)) {
                $header[TargetCheck::COLUMN_TARGET] = Yii::t("app", "Target");
            }

            if (in_array(TargetCheck::COLUMN_NAME, $columns)) {
                $header[TargetCheck::COLUMN_NAME] = Yii::t("app", "Name");
            }

            if (in_array(TargetCheck::COLUMN_REFERENCE, $columns)) {
                $header[TargetCheck::COLUMN_REFERENCE] = Yii::t("app", "Reference");
            }

            $biField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_BACKGROUND_INFO]);

            if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $columns)) {
                $header[TargetCheck::COLUMN_BACKGROUND_INFO] = $biField->localizedTitle;
            }

            $qField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_QUESTION]);

            if (in_array(TargetCheck::COLUMN_QUESTION, $columns)) {
                $header[TargetCheck::COLUMN_QUESTION] = $qField->localizedTitle;
            }

            $rField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_RESULT]);

            if (in_array(TargetCheck::COLUMN_RESULT, $columns)) {
                $header[TargetCheck::COLUMN_RESULT] = $rField->localizedTitle;
            }

            if (in_array(TargetCheck::COLUMN_SOLUTION, $columns)) {
                $header[TargetCheck::COLUMN_SOLUTION] = Yii::t("app", "Solution");
            }

            if (in_array(TargetCheck::COLUMN_ASSIGNED_USER, $columns)) {
                $header[TargetCheck::COLUMN_ASSIGNED_USER] = Yii::t("app", "Assigned");
            }

            if (in_array(TargetCheck::COLUMN_RATING, $columns)) {
                $header[TargetCheck::COLUMN_RATING] = Yii::t("app", "Rating");
            }

            if (in_array(TargetCheck::COLUMN_STATUS, $columns)) {
                $header[TargetCheck::COLUMN_STATUS] = Yii::t("app", "Status");
            }
        }

        // include all PHPExcel libraries
        Yii::setPathOfAlias("xls", Yii::app()->basePath . "/extensions/PHPExcel");
        Yii::import("xls.PHPExcel.Shared.ZipStreamWrapper", true);
        Yii::import("xls.PHPExcel.Shared.String", true);
        Yii::import("xls.PHPExcel", true);
        Yii::registerAutoloader(["PHPExcel_Autoloader", "Load"], true);

        $title = Yii::t("app", "{project} Vulnerability Export", [
            "{project}" => $project->name . " (" . $project->year . ")"
        ]) . " - " . date("Y-m-d");

        $xl = new PHPExcel();

        $xl->getDefaultStyle()->getFont()->setName("Helvetica");
        $xl->getDefaultStyle()->getFont()->setSize(12);
        $xl->getProperties()->setTitle($title);
        $xl->setActiveSheetIndex(0);

        $sheet = $xl->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(30);

        $row  = 1;
        $cols = range("A", "Z");

        if ($header) {
            $col = 0;

            foreach ($header as $type => $value) {
                $sheet->getCell($cols[$col] . $row)->setValue($value);
                $width = 0;

                switch ($type) {
                    case TargetCheck::COLUMN_BACKGROUND_INFO:
                    case TargetCheck::COLUMN_QUESTION:
                    case TargetCheck::COLUMN_RESULT:
                    case TargetCheck::COLUMN_SOLUTION:
                        $width = 30;
                        break;

                    default:
                        $width = 20;
                }

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getRight()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $sheet->getColumnDimension($cols[$col])->setWidth($width);
                $col++;
            }

            $row++;
        }

        $lastCol = $cols[count($header) - 1];
        $range = "A1:" . $lastCol . "1";

        $sheet
            ->getStyle($range)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB("FFE0E0E0");

        $sheet
            ->getStyle($range)
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet
            ->getStyle($range)
            ->getFont()
            ->setBold(true);

        $sheet->getRowDimension(1)->setRowHeight(40);

        foreach ($data as $dataRow) {
            $col = 0;

            foreach ($dataRow as $type => $value) {
                $sheet->getCell($cols[$col] . $row)->setValue("\n" . $value . "\n");

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getRight()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $col++;
            }

            $range = 'A' . $row . ':' . $lastCol . $row;

            $sheet
                ->getStyle($range)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            $sheet
                ->getStyle($range)
                ->getAlignment()
                ->setWrapText(true);

            $sheet
                ->getStyle($range)
                ->getAlignment()
                ->setIndent(1);

            $sheet->getRowDimension($row)->setRowHeight(-1);

            $row++;
        }

        $fileName = $title . ".xlsx";

        // give user a file
        header("Content-Description: File Transfer");
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");

        ob_clean();
        flush();

        $writer = PHPExcel_IOFactory::createWriter($xl, "Excel2007");
        $writer->save("php://output");

        exit();
    }
}