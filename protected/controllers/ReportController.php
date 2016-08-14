<?php

/**
 * Report controller.
 */
class ReportController extends Controller {
    private $project;

    const NORMAL_VULN_LIST = 0;
    const APPENDIX_VULN_LIST = 1;
    const SEPARATE_VULN_LIST = 2;

    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            'https',
			'checkAuth',
            'showReports',
            "idle",
		);
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
     * Generate a vulnerability list.
     */
    private function _generateVulnerabilityList($data, &$section, $sectionNumber, $type = self::NORMAL_VULN_LIST, $ratingImages, $infoLocation = null, $categoryId = null)
    {
        $targetNumber = 1;

        foreach ($data as $target)
        {
            if (!$target['checkCount'])
                continue;

            if ($type == self::SEPARATE_VULN_LIST &&
                !in_array($categoryId, $target['separate']) ||
                $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $target['separateCount'] == $target['info']
            )
                continue;

            if ($type == self::APPENDIX_VULN_LIST && !$target['info'])
                continue;

            if (
                $type == self::NORMAL_VULN_LIST &&
                (
                    $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $target['checkCount'] == $target['info'] + $target['separateCount'] ||
                    $target['checkCount'] == $target['separateCount']
                )
            )
                continue;

            $tableCount = 1;

            if ($type != self::APPENDIX_VULN_LIST && $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE && $target['info'])
                $tableCount = 2;

            for ($tableNumber = 0; $tableNumber < $tableCount; $tableNumber++)
            {
                $subsectionNumber = substr($sectionNumber, strpos($sectionNumber, '.') + 1);

                $this->toc->writeHyperLink(
                    '#vulns_section_' . $subsectionNumber . '_' . $targetNumber,
                    '        ' . $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->textFont
                );

                $section->writeBookmark(
                    'vulns_section_' . $subsectionNumber . '_' . $targetNumber,
                    $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->boldFont
                );

                if ($target['description'])
                {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090');

                    $this->toc->writeText(' / ', $this->textFont);
                    $this->toc->writeHyperLink(
                        '#vulns_section_' . $subsectionNumber . '_' . $targetNumber,
                        $target['description'],
                        $font
                    );

                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], $font);
                }

                if ($tableNumber == 1)
                    $section->writeText(' - ' . Yii::t('app', 'Info Checks'), $this->textFont);

                $section->writeText("\n", $this->textFont);
                $this->toc->writeText("\n", $this->textFont);

                $targetNumber++;

                $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                $table->addColumnsList(array( $this->docWidth * 0.17, $this->docWidth * 0.83 ));

                $row = 1;

                foreach ($target['categories'] as $category)
                {
                    if (
                        $type == self::SEPARATE_VULN_LIST &&
                        (
                            $category['id'] != $categoryId ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $category['info'] == $category['separate'] ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                            (
                                $tableNumber == 0 && $category['separate'] == $category['info'] ||
                                $tableNumber == 1 && !$category['info']
                            )
                        )
                    )
                        continue;

                    if ($type == self::APPENDIX_VULN_LIST && !$category['info'])
                        continue;

                    if (
                        $type == self::NORMAL_VULN_LIST &&
                        (
                            $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $category['checkCount'] == $category['info'] + $category['separate'] ||
                            $category['checkCount'] == $category['separate'] ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                            (
                                $tableNumber == 0 && $category['checkCount'] == $category['info'] + $category['separate'] ||
                                $tableNumber == 1 && !$category['info']
                            )
                        )
                    )
                        continue;

                    $table->addRow();
                    $table->mergeCellRange($row, 1, $row, 2);

                    $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                    $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                    $table->setBackgroundForCellRange('#B0B0B0', $row, 1, $row, 1);
                    $table->writeToCell($row, 1, $category['name']);

                    $row++;

                    foreach ($category['controls'] as $control)
                    {
                        if (
                            $type == self::SEPARATE_VULN_LIST &&
                            (
                                !$control['separate'] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $control['info'] == $control['separate'] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                (
                                    $tableNumber == 0 && $control['separate'] == $control['info'] ||
                                    $tableNumber == 1 && !$control['info']
                                )
                            )
                        )
                            continue;

                        if ($type == self::APPENDIX_VULN_LIST && !$control['info'])
                            continue;

                        if (
                            $type == self::NORMAL_VULN_LIST &&
                            (
                                $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $control['checkCount'] == $control['info'] + $control['separate'] ||
                                $control['checkCount'] == $control['separate'] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                (
                                    $tableNumber == 0 && $control['checkCount'] == $control['info'] + $control['separate'] ||
                                    $tableNumber == 1 && !$control['info']
                                )
                            )
                        )
                            continue;

                        $table->addRow();
                        $table->mergeCellRange($row, 1, $row, 2);

                        $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                        $table->getCell($row, 1)->setBorder($this->thinBorder);
                        $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                        $table->setBackgroundForCellRange('#D0D0D0', $row, 1, $row, 1);
                        $table->writeToCell($row, 1, $control['name']);

                        $row++;

                        foreach ($control['checks'] as $check)
                        {
                            if (
                                $type == self::SEPARATE_VULN_LIST &&
                                (
                                    !$check['separate'] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $check['info'] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                    (
                                        $tableNumber == 0 && $check['info'] ||
                                        $tableNumber == 1 && !$check['info']
                                    )
                                )
                            )
                                continue;

                            if ($type == self::APPENDIX_VULN_LIST && !$check['info'])
                                continue;

                            if (
                                $type == self::NORMAL_VULN_LIST &&
                                (
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $check['info'] ||
                                    $check['separate'] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                    (
                                        $tableNumber == 0 && $check['info'] ||
                                        $tableNumber == 1 && !$check['info']
                                    )
                                )
                            )
                                continue;

                            $table->addRow();
                            $table->mergeCellRange($row, 1, $row, 2);

                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                            $table->setBackgroundForCellRange('#F0F0F0', $row, 1, $row, 1);

                            $table->getCell($row, 1)->writeBookmark(
                                'check_' . $target['id'] . '_' . $check['id'],
                                $check['name']
                            );

                            $row++;

                            // reference info
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 2)->setBorder($this->thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Reference'));

                            $reference    = $check['reference'] . ( $check['referenceCode'] ? '-' . $check['referenceCode'] : '' );
                            $referenceUrl = '';

                            if ($check['referenceCode'] && $check['referenceCodeUrl'])
                                $referenceUrl = $check['referenceCodeUrl'];
                            else if ($check['referenceUrl'])
                                $referenceUrl = $check['referenceUrl'];

                            if ($referenceUrl)
                                $table->getCell($row, 2)->writeHyperLink($referenceUrl, $reference, $this->linkFont);
                            else
                                $table->writeToCell($row, 2, $reference);

                            $row++;

                            if ($check['tableResult']) {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorderBR);

                                if ($check['result']) {
                                    $table->mergeCellRange($row - 1, 1, $row, 1);
                                } else {
                                    $table->writeToCell($row, 1, Yii::t('app', 'Result'));
                                }

                                $tableResult = new ResultTable();
                                $tableResult->parse($check['tableResult']);

                                foreach ($tableResult->getTables() as $tResult) {
                                    $nestedTable = $table->getCell($row, 2)->addTable();
                                    $nestedTable->addRows($tResult["rowCount"] + 1);

                                    $columnWidths = array();
                                    $tableWidth = $this->docWidth * 0.83 - $this->cellPadding * 2;

                                    foreach ($tResult["columns"] as $column) {
                                        $columnWidths[] = (float)$column['width'] * $tableWidth;
                                    }

                                    $nestedTable->addColumnsList($columnWidths);

                                    $nestedTable->setFontForCellRange($this->boldFont, 1, 1, 1, $tResult["columnCount"]);
                                    $nestedTable->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, $tResult["columnCount"]);
                                    $nestedTable->setFontForCellRange($this->textFont, 2, 1, $tResult["rowCount"] + 1, $tResult["columnCount"]);
                                    $nestedTable->setBorderForCellRange($this->thinBorder, 1, 1, $tResult["rowCount"] + 1, $tResult["columnCount"]);
                                    $nestedTable->setFirstRowAsHeader();

                                    $nestedRow = 1;
                                    $nestedColumn = 1;

                                    foreach ($tResult["columns"] as $column) {
                                        $nestedTable->getCell($nestedRow, $nestedColumn)->setCellPaddings(
                                            $this->cellPadding,
                                            $this->cellPadding,
                                            $this->cellPadding,
                                            $this->cellPadding
                                        );

                                        $nestedTable->getCell($nestedRow, $nestedColumn)->setVerticalAlignment(
                                            PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP
                                        );

                                        $nestedTable->writeToCell($nestedRow, $nestedColumn, $column['name']);
                                        $nestedColumn++;
                                    }

                                    foreach ($tResult["data"] as $dataRow) {
                                        $nestedRow++;
                                        $nestedColumn = 1;

                                        foreach ($dataRow as $dataCell) {
                                            $nestedTable->getCell($nestedRow, $nestedColumn)->setCellPaddings(
                                                $this->cellPadding,
                                                $this->cellPadding,
                                                $this->cellPadding,
                                                $this->cellPadding
                                            );

                                            $nestedTable->getCell($nestedRow, $nestedColumn)->setVerticalAlignment(
                                                PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP
                                            );

                                            $nestedTable->writeToCell($nestedRow, $nestedColumn, $dataCell);
                                            $nestedColumn++;
                                        }
                                    }

                                    $table->writeToCell($row, 2, "\n");
                                }

                                $row++;
                            }

                            if (isset($check["fields"])) {
                                foreach ($check["fields"] as $field) {
                                    $table->addRow();
                                    $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $table->writeToCell($row, 1, $field["title"]);

                                    if (Utils::isHtml($field["value"])) {
                                        $this->_renderText($table->getCell($row, 2), $field["value"], false);
                                    } else {
                                        $table->writeToCell($row, 2, $field["value"]);
                                    }

                                    $row++;
                                }
                            }

                            if ($check['solutions']) {
                                $table->addRows(count($check['solutions']));

                                $table->mergeCellRange($row, 1, $row + count($check['solutions']) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->writeToCell($row, 1, Yii::t('app', 'Solutions'));

                                foreach ($check['solutions'] as $solution)
                                {
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $cell = $table->getCell($row, 2);
                                    $this->_renderText($cell, $solution, false);

                                    $row++;
                                }
                            }

                            if ($check['images']) {
                                $table->addRows(count($check['images']));

                                $table->mergeCellRange($row, 1, $row + count($check['images']) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->writeToCell($row, 1, Yii::t('app', 'Attachments'));

                                foreach ($check['images'] as $image) {
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $table->writeToCell($row, 2, $image['title']);
                                    $table->addImageToCell($row, 2, $image['image'], new PHPRtfLite_ParFormat(), $this->docWidth * 0.78);

                                    $row++;
                                }
                            }

                            $table->addRow();

                            // rating
                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 2)->setBorder($this->thinBorder);

                            $table->writeToCell($row, 1, Yii::t("app", "Rating"));
                            $image = null;

                            switch ($check["rating"]) {
                                case TargetCheck::RATING_HIGH_RISK:
                                    $image = $ratingImages[TargetCheck::RATING_HIGH_RISK];
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $image = $ratingImages[TargetCheck::RATING_MED_RISK];
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $image = $ratingImages[TargetCheck::RATING_LOW_RISK];
                                    break;

                                case TargetCheck::RATING_NONE:
                                    $image = $ratingImages[TargetCheck::RATING_NONE];
                                    break;

                                case TargetCheck::RATING_NO_VULNERABILITY:
                                    $image = $ratingImages[TargetCheck::RATING_NO_VULNERABILITY];
                                    break;

                                case TargetCheck::RATING_INFO:
                                    $image = $ratingImages[TargetCheck::RATING_INFO];
                                    break;
                            }

                            $table->addImageToCell($row, 2, $image);
                            $table->writeToCell($row, 2, " " . $check["ratingName"]);

                            $row++;
                        }
                    }
                }
            }
        }
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
     * Generate project function.
     * @param ProjectReportForm $form
     */
    private function _generateProjectReport($form) {
        $clientId = $form->clientId;
        $projectId = $form->projectId;
        $targetIds = $form->targetIds;
        $options = $form->options;
        $templateId = $form->templateId;
        $fields = $form->fields;

        if (!$fields) {
            $fields = array();
        }

        if (!$options) {
            $options = array();
        }

        $project = Project::model()->with(array(
            "projectUsers" => array(
                "with" => "user"
            ),
            "client",
            "targets",
        ))->findByAttributes(array(
            "client_id" => $clientId,
            "id" => $projectId
        ));

        if ($project === null) {
            Yii::app()->user->setFlash("error", Yii::t("app", "Project not found."));
            return;
        }

        if (!$project->checkPermission()) {
            Yii::app()->user->setFlash("error", Yii::t("app", "Access denied."));
            return;
        }

        $this->project = array(
            "project" => $project
        );

        if (!$targetIds || !count($targetIds)) {
            Yii::app()->user->setFlash("error", Yii::t("app", "Please select at least 1 target."));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $template = ReportTemplate::model()->with(array(
            "l10n" => [
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => ["language_id" => $language],
            ],
            "summary" => [
                "with" => [
                    "l10n" => [
                        "alias" => "summary_l10n",
                        "on" => "summary_l10n.language_id = :language_id",
                        "params" => ["language_id" => $language],
                    ]
                ]
            ],
            "vulnSections" => [
                "alias" => "vs",
                "order" => "vs.sort_order ASC",
                "with" => [
                    "l10n" => [
                        "alias" => "section_l10n",
                        "on" => "section_l10n.language_id = :language_id",
                        "params" => ["language_id" => $language]
                    ]
                ]
            ],
            "sections" => [
                "alias" => "s",
                "order" => "s.sort_order ASC",
            ]
        ))->findByPk($templateId);

        if ($template === null) {
            Yii::app()->user->setFlash("error", Yii::t("app", "Template not found."));
            return;
        }

        $templateCategoryIds = array();

        foreach ($template->vulnSections as $section) {
            $templateCategoryIds[] = $section->check_category_id;
        }

        FileManager::createDir(Yii::app()->params["reports"]["tmpFilesPath"], 0777);

        $prm = new ProjectReportManager();
        $data = $prm->getProjectReportData($targetIds, $templateCategoryIds, $project, $fields, $language);
        $data["pageMargin"] = $form->pageMargin;
        $data["cellPadding"] = $form->cellPadding;
        $data["fontSize"] = $form->fontSize;
        $data["fontFamily"] = $form->fontFamily;
        $data["options"] = $options;

        $plugin = ReportPlugin::getPlugin($template, $data);
        $plugin->generate();
        $plugin->sendOverHttp();

        exit();
    }

    /**
     * Gives report file to user
     * @param $filepath
     * @param null $attachments
     */
    private function _generateReportFile ($data, $attachments = null) {
        if ($attachments !== null) {
            $reportFileName = $data['name'];
            $reportFilePath = $data['path'];
            $fileName = $data['zipName'];
            $hashName = hash('sha256', rand() . time() . $fileName);
            $filePath = Yii::app()->params['reports']['tmpFilesPath'] . '/' . $hashName;

            $zip = new ZipArchive();

            if ($zip->open($filePath, ZipArchive::CREATE) !== true) {
                throw new Exception("Unable to create report archive: $fileName");
            }

            FileManager::zipFile($zip, $reportFilePath, $reportFileName);

            $zip->addEmptyDir('attachments');

            foreach ($attachments as $attachment) {
                $hostDir = 'attachments/' . $attachment['host'];

                if (!$zip->locateName($hostDir)) {
                    $zip->addEmptyDir($hostDir);
                }

                FileManager::zipFile($zip, $attachment["path"], $hostDir . '/' . $attachment["filename"]);
            }

            $zip->close();
        } else {
            $fileName = $data['name'];
            $filePath = $data['path'];
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);
        FileManager::unlink($filePath);
    }

    private function _generateFileAttachmentsList ($attachments) {
        $text = "<br/>";

        foreach ($attachments as $attachment) {
            $text = $text . sprintf("Host '%s'  -  Check '%s'  -  GTTA File '%s' attached <br/>",
                    $attachment["host"],
                    $attachment["check"],
                    $attachment["filename"]
                );
        }

        return $text;
    }

    /**
     * Show project report form.
     */
    public function actionProject() {
        $form = new ProjectReportForm();

        if (isset($_POST["ProjectReportForm"])) {
            $form->attributes = $_POST["ProjectReportForm"];

            if ($form->validate()) {
                $this->_generateProjectReport($form);
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = "t.name ASC";

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with("project")->findAllByAttributes(array(
                "user_id" => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition("id", $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->order = "COALESCE(l10n.name, t.name) ASC";
        $criteria->together = true;

        $templates = ReportTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on"       => "language_id = :language_id",
                "params"   => array( "language_id" => $language )
            )
        ))->findAll($criteria);
        
        $lang = [
            "l10n" => [
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => ["language_id" => $language]
            ]
        ];

        $riskTemplates = RiskTemplate::model()->with($lang)->findAllByAttributes(
            array(),
            array("order" => "COALESCE(l10n.name, t.name) ASC")
        );

        $fields = GlobalCheckField::model()->with($lang)->findAll(["order" => "sort_order ASC"]);

        $this->breadcrumbs[] = array(Yii::t("app", "Project Report"), "");

        // display the report generation form
        $this->pageTitle = Yii::t("app", "Project Report");
		$this->render("project", array(
            "model" => $form,
            "clients" => $clients,
            "templates" => $templates,
            "riskTemplates" => $riskTemplates,
            "fields" => $fields,
            "infoChecksLocation" => array(
                ProjectReportForm::INFO_LOCATION_TARGET => Yii::t("app", "in the main list"),
                ProjectReportForm::INFO_LOCATION_TABLE => Yii::t("app", "in a separate table"),
                ProjectReportForm::INFO_LOCATION_APPENDIX => Yii::t("app", "in the appendix"),
            ),
        ));
    }

    /**
     * Comparison report
     * @param $project1
     * @param $project2
     * @return array
     * @throws CHttpException
     */
    private function _comparisonReport($project1, $project2) {
        $targets1 = Target::model()->findAllByAttributes(
            array('project_id' => $project1->id),
            array('order' => 't.host ASC')
        );

        $targets2 = Target::model()->findAllByAttributes(
            array('project_id' => $project2->id),
            array('order' => 't.host ASC')
        );

        // find corresponding targets
        $data = array();

        foreach ($targets1 as $target1) {
            foreach ($targets2 as $target2) {
                if ($target2->hostPort == $target1->hostPort) {
                    $data[] = array(
                        $target1,
                        $target2
                    );

                    break;
                }
            }
        }

        if (!$data) {
            throw new CHttpException(404, Yii::t('app', 'No targets to compare.'));
        }

        $targetsData = array();

        foreach ($data as $targets) {
            $targetData = array(
                'host' => $targets[0]->hostPort,
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
     * Generate comparison report.
     * @param ProjectComparisonForm $form
     */
    private function _generateComparisonReport(ProjectComparisonForm $form) {
        $clientId = $form->clientId;
        $projectId1 = $form->projectId1;
        $projectId2 = $form->projectId2;

        $project1 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId1
        ));

        if ($project1 === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'First project not found.'));
            return;
        }

        if (!$project1->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $project2 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId2
        ));

        if ($project2 === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Second project not found.'));
            return;
        }

        if (!$project2->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $targetsData = $this->_comparisonReport($project1, $project2);

        $r = new RtfReport();
        $r->setup($form->pageMargin, $form->cellPadding, $form->fontSize, $form->fontSize);
        $section = $r->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText(Yii::t('app', 'Projects Comparison') . ', ', $r->textFont, $r->noPar);
        $footer->writePlainRtfCode(
            '\fs' . ($r->textFont->getSize() * 2) . ' \f' . $r->textFont->getFontIndex() . ' ' .
             Yii::t('app', 'page {page} of {numPages}',
            array(
                '{page}' => '{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}',
                '{numPages}' => '{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}'
            )
        ));

        // title
        $section->writeText(Yii::t('app', 'Projects Comparison'), $r->h1Font, $r->titlePar);

        // detailed summary
        $section->writeText(Yii::t('app', 'Target Comparison') . '<br>', $r->h3Font, $r->noPar);
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($targetsData) + 1);
        $table->addColumnsList(array( $r->docWidth * 0.33, $r->docWidth * 0.33, $r->docWidth * 0.34 ));
        $table->setFontForCellRange($r->boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
        $table->setFontForCellRange($r->textFont, 2, 1, count($targetsData) + 1, 3);
        $table->setBorderForCellRange($r->thinBorder, 1, 1, count($targetsData) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($targetsData) + 1; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                $table->getCell($row, $col)->setCellPaddings(
                    $form->cellPadding,
                    $form->cellPadding,
                    $form->cellPadding,
                    $form->cellPadding
                );

                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            }
        }

        $table->writeToCell(1, 1, Yii::t('app', 'Target'));
        $table->writeToCell(1, 2, $project1->name . ' (' . $project1->year . ')');
        $table->writeToCell(1, 3, $project2->name . ' (' . $project2->year . ')');

        $row = 2;
        $system = System::model()->findByPk(1);

        foreach ($targetsData as $target) {
            $table->writeToCell($row, 1, $target['host']);
            $table->addImageToCell($row, 2, $r->generateRatingImage($target['ratings'][0]), null, $r->docWidth * 0.30);
            $table->addImageToCell($row, 3, $r->generateRatingImage($target['ratings'][1]), null, $r->docWidth * 0.30);

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
            $table->getCell($row, 3)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        $fileName = Yii::t('app', 'Projects Comparison') . '.rtf';
        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['reports']['tmpFilesPath'] . '/' . $hashName;

        $r->rtf->save($filePath);

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Show comparison report form.
     */
    public function actionComparison() {
        $form = new ProjectComparisonForm();

        if (isset($_POST['ProjectComparisonForm'])) {
            $form->attributes = $_POST['ProjectComparisonForm'];

            if ($form->validate()) {
                $this->_generateComparisonReport($form);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);
        $this->breadcrumbs[] = array(Yii::t('app', 'Projects Comparison'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Projects Comparison');
		$this->render('comparison', array(
            'model' => $form,
            'clients' => $clients
        ));
    }

    /**
     * Fulfillment degree image.
     */
    private function _generateFulfillmentImage($degree)
    {
        $scale = imagecreatefrompng(Yii::app()->basePath . '/../images/fulfillment-stripe.png');
        imagealphablending($scale, false);
        imagesavealpha($scale, true);

        $image = imagecreatetruecolor(301, 30);

        $lineCoord = $degree * 3;
        $white     = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        $color     = imagecolorallocate($image, 0x3A, 0x87, 0xAD);

        imagefilledrectangle($image, 0, 0, 301, 30, $white);
        imagefilledrectangle($image, 0, 6, $lineCoord, 24, $color);
        imagecopyresampled($image, $scale, 0, 0, 0, 0, 301, 30, 301, 30);

        $hashName = hash('sha256', rand() . time() . rand());
        $filePath = Yii::app()->params['reports']['tmpFilesPath'] . '/' . $hashName . '.png';

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * Sort controls.
     */
    public static function sortControls($a, $b) {
        return $a['degree'] > $b['degree'];
    }

    /**
     * Fulfillment report
     * @param $fullReport
     * @param $model
     * @param $language
     * @return array
     */
    private function _fulfillmentReport($fullReport, $model, $language) {
        $data = array();

        if (!$fullReport) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $model->targetIds);
            $criteria->addColumnCondition(array('project_id' => $model->projectId));
            $criteria->order = 't.host ASC';
            $targets = Target::model()->findAll($criteria);
        } else {
            $targets = $this->project['targets'];
        }

        foreach ($targets as $target) {
            $targetData = array(
                'id'          => $target->id,
                'host'        => $target->hostPort,
                'description' => $target->description,
                'controls'    => array(),
            );

            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            // get all categories
            $categories = TargetCheckCategory::model()->with(array(
                'category' => array(
                    'with' => array(
                        'l10n' => array(
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'language_id = :language_id',
                            'params'   => array( 'language_id' => $language )
                        )
                    )
                )
            ))->findAllByAttributes(
                array( 'target_id' => $target->id  ),
                array( 'order'     => 'COALESCE(l10n.name, category.name) ASC' )
            );

            foreach ($categories as $category) {
                // get all controls
                $controls = CheckControl::model()->with(array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                ))->findAllByAttributes(
                    array( 'check_category_id' => $category->check_category_id ),
                    array( 'order'             => 't.sort_order ASC' )
                );

                if (!$controls) {
                    continue;
                }

                foreach ($controls as $control) {
                    $controlData = array(
                        'name'   => $category->category->localizedName . ' / ' . $control->localizedName,
                        'degree' => 0.0,
                    );

                    $criteria = new CDbCriteria();

                    $criteria->addInCondition('t.reference_id', $referenceIds);
                    $criteria->addColumnCondition(array(
                        't.check_control_id' => $control->id
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
                                    $controlData['degree'] += 0;
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $controlData['degree'] += 1;
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $controlData['degree'] += 2;
                                    break;

                                case TargetCheck::RATING_HIGH_RISK:
                                    $controlData['degree'] += 3;
                                    break;
                            }
                        }
                    }

                    $maxDegree = count($checks) * 3;
                    $controlData['degree'] = round(100 - $controlData['degree'] / $maxDegree * 100);
                    $targetData['controls'][] = $controlData;
                }
            }

            $data[] = $targetData;
        }

        return $data;
    }

    /**
     * Generate a Degree of Fulfillment report
     * @param FulfillmentDegreeForm $form
     */
    private function _generateFulfillmentDegreeReport(FulfillmentDegreeForm $form = null, &$section = null, $sectionNumber = null) {
        $fullReport = true;

        if (!$section) {
            $fullReport = false;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if (!$fullReport) {
            $project = Project::model()->findByAttributes(array(
                'client_id' => $form->clientId,
                'id' => $form->projectId
            ));

            if ($project === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
                return;
            }

            if (!$project->checkPermission()) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
                return;
            }

            if (!$form->targetIds || !count($form->targetIds)) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
                return;
            }
        } else {
            $project = $this->project['project'];
        }

        if (!$fullReport) {
            $r = new RtfReport();
            $r->setup($form->pageMargin, $form->cellPadding, $form->fontSize, $form->fontFamily);
            $section = $r->rtf->addSection();

            // footer
            $footer = $section->addFooter();
            $footer->writeText(Yii::t('app', 'Degree of Fulfillment') . ': ' . $project->name . ', ', $r->textFont, $r->noPar);
            $footer->writePlainRtfCode(
                '\fs' . ($r->textFont->getSize() * 2) . ' \f' . $r->textFont->getFontIndex() . ' ' .
                 Yii::t('app', 'page {page} of {numPages}',
                array(
                    '{page}'     => '{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}',
                    '{numPages}' => '{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}'
                )
            ));

            // title
            $section->writeText(Yii::t('app', 'Degree of Fulfillment') . ': ' . $project->name, $r->h1Font, $r->titlePar);
            $section->writeText("\n\n");
        }

        $data = $this->_fulfillmentReport($fullReport, $form, $language);

        $targetNumber = 1;

        foreach ($data as $target) {
            if ($fullReport) {
                $this->toc->writeHyperLink(
                    '#degree_' . $targetNumber,
                    '        ' . $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->textFont
                );

                $section->writeBookmark(
                    'degree_' . $targetNumber,
                    $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->boldFont
                );

                if ($target['description']) {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090');

                    $this->toc->writeText(' / ', $this->textFont);
                    $this->toc->writeHyperLink(
                        '#degree_' . $targetNumber,
                        $target['description'],
                        $font
                    );
                    $this->toc->writeText("\n");

                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], $font);
                } else {
                    $this->toc->writeText("\n");
                }

                $targetNumber++;
            } else {
                $section->writeText($target['host'], $this->h3Font);

                if ($target['description']) {
                    $section->writeText(' / ', $this->h3Font);
                    $section->writeText($target['description'], new PHPRtfLite_Font($this->h3Font->getSize(), $this->fontFamily, '#909090'));
                }
            }

            $section->writeText("\n");

            if (!count($target['controls'])) {
                $section->writeText("\n", $this->textFont);
                $section->writeText(Yii::t('app', 'No checks.') . "\n\n", $this->textFont);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList(array( $this->docWidth * 0.28, $this->docWidth * 0.56, $this->docWidth * 0.16 ));

            $row = 1;

            $table->addRow();
            $table->mergeCellRange(1, 2, 1, 3);

            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
            $table->getCell($row, 2)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
            $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
            $table->setBorderForCellRange($this->thinBorder, 1, 1, 1, 3);
            $table->setFirstRowAsHeader();

            $table->writeToCell($row, 1, Yii::t('app', 'Control'));
            $table->writeToCell($row, 2, Yii::t('app', 'Degree of Fulfillment'));

            $row++;

            usort($target['controls'], array('ReportController', 'sortControls'));

            foreach ($target['controls'] as $control) {
                $table->addRow();
                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                $table->getCell($row, 1)->setBorder($this->thinBorder);

                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 2)->setBorder($this->thinBorder);
                $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                $table->getCell($row, 3)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 3)->setBorder($this->thinBorder);

                $table->writeToCell($row, 1, $control['name'], $this->textFont);
                $table->addImageToCell($row, 2, $this->_generateFulfillmentImage($control['degree']), null, $this->docWidth * 0.50);
                $table->writeToCell($row, 3, $control['degree'] . '%');

                $row++;
            }

            $table->setFontForCellRange($this->textFont, 1, 1, count($target['controls']) + 1, 2);
        }

        if (!$fullReport) {
            $fileName = Yii::t('app', 'Degree of Fulfillment') . ' - ' . $project->name . ' (' . $project->year . ').rtf';
            $hashName = hash('sha256', rand() . time() . $fileName);
            $filePath = Yii::app()->params['reports']['tmpFilesPath'] . '/' . $hashName;

            $this->rtf->save($filePath);

            // give user a file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));

            ob_clean();
            flush();

            readfile($filePath);

            exit();
        }
    }

    /**
     * Show degree of fulfillment report form.
     */
    public function actionFulfillment() {
        $form = new FulfillmentDegreeForm();

        if (isset($_POST['FulfillmentDegreeForm'])) {
            $form->attributes = $_POST['FulfillmentDegreeForm'];

            if ($form->validate()) {
                $this->_generateFulfillmentDegreeReport($form);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Degree of Fulfillment'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Degree of Fulfillment');
		$this->render('fulfillment', array(
            'model'   => $form,
            'clients' => $clients
        ));
    }

    /**
     * Risk matrix report
     * @param $fullReport
     * @param $model
     * @param $risks
     * @param $language
     * @return array
     */
    private function _riskMatrixReport($fullReport, $model, $risks, $language) {
        $data = array();

        if (!$fullReport) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $model->targetIds);
            $criteria->addColumnCondition(array('project_id' => $model->projectId));
            $criteria->order = 't.host ASC';
            $targets = Target::model()->findAll($criteria);
        } else {
            $targets = $this->project['targets'];
        }

        foreach ($targets as $target) {
            $mtrx = array();
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            foreach ($target->_categories as $category) {
                $controlIds = array();

                $controls = CheckControl::model()->findAllByAttributes(array(
                    'check_category_id' => $category->check_category_id
                ));

                foreach ($controls as $control) {
                    $controlIds[] = $control->id;
                }

                $criteria = new CDbCriteria();
                $criteria->order = 't.sort_order ASC';
                $criteria->addInCondition('t.reference_id', $referenceIds);
                $criteria->addInCondition('t.check_control_id', $controlIds);
                $criteria->together = true;

                $checks = Check::model()->with(array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on' => 'l10n.language_id = :language_id',
                        'params' => array( 'language_id' => $language )
                    ),
                    'targetChecks' => array(
                        'alias' => 'tcs',
                        'joinType' => 'INNER JOIN',
                        'on' => 'tcs.target_id = :target_id AND tcs.status = :status AND (tcs.rating = :high OR tcs.rating = :med)',
                        'params' => array(
                            'target_id' => $target->id,
                            'status' => TargetCheck::STATUS_FINISHED,
                            'high' => TargetCheck::RATING_HIGH_RISK,
                            'med' => TargetCheck::RATING_MED_RISK,
                        ),
                    )
                ))->findAll($criteria);

                foreach ($checks as $check) {
                    if (!isset($model->matrix[$target->id][$check->id])) {
                        continue;
                    }

                    $ctr = 0;

                    foreach ($risks as $riskId => $risk) {
                        $ctr++;

                        if (!isset($model->matrix[$target->id][$check->id][$risk->id])) {
                            continue;
                        }

                        $riskName = 'R' . $ctr;

                        $damage = $model->matrix[$target->id][$check->id][$risk->id]['damage'] - 1;
                        $likelihood = $model->matrix[$target->id][$check->id][$risk->id]['likelihood'] - 1;

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
                'host' => $target->hostPort,
                'description' => $target->description,
                'matrix' => $mtrx
            );
        }

        return $data;
    }

    /**
     * Generate risk matrix report.
     * @param RiskMatrixForm $form
     */
    private function _generateRiskMatrixReport(RiskMatrixForm $form, &$section = null, $sectionNumber = null) {
        $fullReport = true;

        if (!$section) {
            $fullReport = false;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if (!$fullReport) {
            $template = RiskTemplate::model()->findByAttributes(array(
                'id' => $form->templateId
            ));

            if ($template === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Template not found.'));
                return;
            }

            $project = Project::model()->findByAttributes(array(
                'client_id' => $form->clientId,
                'id' => $form->projectId
            ));

            if ($project === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
                return;
            }

            if (!$project->checkPermission()) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
                return;
            }

            if (!$form->targetIds || !count($form->targetIds)) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
                return;
            }
        } else {
            $template = RiskTemplate::model()->findByAttributes(array(
                'id' => $form->templateId
            ));

            if ($template === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Template not found.'));
                return;
            }

            $project = $this->project['project'];
        }

        $risks = RiskCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAllByAttributes(
            array('risk_template_id' => $template->id),
            array('order' => 'COALESCE(l10n.name, t.name) ASC')
        );

        $data = $this->_riskMatrixReport($fullReport, $form, $risks, $language);

        if (!$fullReport) {
            $r = new RtfReport();
            $r->setup($form->pageMargin, $form->cellPadding, $form->fontSize, $form->fontFamily);
            $section = $r->rtf->addSection();

            // footer
            $footer = $section->addFooter();
            $footer->writeText(Yii::t('app', 'Risk Matrix') . ': ' . $project->name . ', ', $r->textFont, $r->noPar);
            $footer->writePlainRtfCode(
                '\fs' . ($r->textFont->getSize() * 2) . ' \f' . $r->textFont->getFontIndex() . ' ' .
                 Yii::t('app', 'page {page} of {numPages}',
                array(
                    '{page}'     => '{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}',
                    '{numPages}' => '{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}'
                )
            ));

            // title
            $section->writeText(Yii::t('app', 'Risk Matrix') . ': ' . $project->name, $r->h1Font, $r->titlePar);
            $section->writeText(Yii::t('app', 'Risk Categories') . "\n", $r->h3Font, $r->noPar);
        }

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
        $table->addRows(count($risks) + 1);
        $table->addColumnsList(array( $this->docWidth * 0.11, $this->docWidth * 0.89 ));
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 2);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 2);
        $table->setFontForCellRange($this->textFont, 2, 1, count($risks), 2);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, count($risks) + 1, 2);

        // set paddings
        for ($row = 1; $row <= count($risks) + 1; $row++) {
            for ($col = 1; $col <= 2; $col++) {
                $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
            }
        }

        $row = 1;

        $table->writeToCell($row, 1, Yii::t('app', 'Code'), $this->boldFont);
        $table->writeToCell($row, 2, Yii::t('app', 'Risk Category'), $this->boldFont);

        $row++;
        $ctr = 0;

        foreach ($risks as $risk) {
            $ctr++;

            $table->writeToCell($row, 1, 'R' . $ctr);
            $table->writeToCell($row, 2, $risk->localizedName);

            $row++;
        }

        if (!$fullReport) {
            $section->writeText(Yii::t('app', 'Targets'), $this->h3Font, $this->h3Par);
            $section->writeText("\n\n", $this->textFont);
        }

        $targetNumber = 1;

        foreach ($data as $target) {
            if ($fullReport) {
                $this->toc->writeHyperLink(
                    '#risk_matrix_' . $targetNumber,
                    '        ' . $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->textFont
                );

                $section->writeBookmark(
                    'risk_matrix_' . $targetNumber,
                    $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->boldFont
                );

                if ($target['description']) {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090');

                    $this->toc->writeText(' / ', $this->textFont);
                    $this->toc->writeHyperLink(
                        '#risk_matrix_' . $targetNumber,
                        $target['description'],
                        $font
                    );
                    $this->toc->writeText("\n");

                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], $font);
                } else {
                    $this->toc->writeText("\n");
                }

                $targetNumber++;
            } else {
                $section->writeText($target['host'], $this->boldFont);

                if ($target['description']) {
                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                }
            }

            $section->writeText("\n");

            if (!$target['matrix']) {
                $section->writeText("\n", $this->textFont);
                $section->writeText(Yii::t('app', 'No checks.') . "\n\n", $this->textFont);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

            $table->addRows(5);
            $table->addColumnsList(array( $this->docWidth * 0.12, $this->docWidth * 0.22, $this->docWidth * 0.22, $this->docWidth * 0.22, $this->docWidth * 0.22 ));

            $table->mergeCellRange(1, 1, 4, 1);
            $table->mergeCellRange(5, 1, 5, 5);

            $table->setFontForCellRange($this->smallBoldFont, 1, 1, 5, 1);
            $table->setFontForCellRange($this->smallBoldFont, 5, 1, 5, 1);
            $table->setBorderForCellRange($this->thinBorderTL, 1, 1, 5, 1);
            $table->setBorderForCellRange($this->thinBorderBR, 5, 1, 5, 5);

            $table->setFontForCellRange($this->textFont, 1, 2, 4, 5);
            $table->setBorderForCellRange($this->thinBorder, 1, 2, 4, 5);

            $table->writeToCell(1, 1, '&uarr;<br>' . Yii::t('app', 'Damage'));
            $table->writeToCell(5, 1, Yii::t('app', 'Likelihood') . ' &rarr;');

            // set paddings
            for ($row = 1; $row <= 5; $row++) {
                for ($col = 1; $col <= 5; $col++) {
                    $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);

                    if ($col == 1 || $row == 5) {
                        $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        $table->getCell($row, $col)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                        continue;
                    }

                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                    $table->getCell($row, $col)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                    $bgColor = '#CCFFBB';

                    if (($row == 1 && $col >= 3) || ($row == 2 && $col >= 4) || ($row == 3 && $col >= 5)) {
                        $bgColor = '#FFBBBB';
                    }

                    $table->getCell($row, $col)->setBackgroundColor($bgColor);
                }
            }

            $matrix = $target['matrix'];

            for ($damage = 0; $damage < 4; $damage++) {
                for ($likelihood = 0; $likelihood < 4; $likelihood++) {
                    if (!isset($matrix[$damage][$likelihood])) {
                        continue;
                    }

                    $text = implode(', ', $matrix[$damage][$likelihood]);
                    $table->writeToCell(4 - $damage, $likelihood + 2, $text);
                }
            }
        }

        if (!$fullReport) {
            $fileName = Yii::t('app', 'Risk Matrix') . ' - ' . $project->name . ' (' . $project->year . ').rtf';
            $hashName = hash('sha256', rand() . time() . $fileName);
            $filePath = Yii::app()->params['reports']['tmpFilesPath'] . '/' . $hashName;

            $r->rtf->save($filePath);

            // give user a file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));

            ob_clean();
            flush();

            readfile($filePath);

            exit();
        }
    }

    /**
     * Show risk matrix report form.
     */
    public function actionRiskMatrix() {
        $form = new RiskMatrixForm();

        if (isset($_POST['RiskMatrixForm'])) {
            $form->attributes = $_POST['RiskMatrixForm'];

            if ($form->validate()) {
                $this->_generateRiskMatrixReport($form);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Matrix'), '');

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $templates = RiskTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findAllByAttributes(
            array(),
            array('order' => 'COALESCE(l10n.name, t.name) ASC')
        );

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Risk Matrix');
		$this->render('risk_matrix', array(
            'model'     => $form,
            'clients'   => $clients,
            'templates' => $templates
        ));
    }

    /**
     * Display an effort estimation form.
     */
	public function actionEffort()
	{
        $references = Reference::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAllByAttributes(
            array(),
            array( 'order' => 'COALESCE(l10n.name, t.name) ASC' )
        );

        $checks = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),
            'control'
        ))->findAllByAttributes(
            array(),
            array( 'order' => 't.sort_order ASC' )
        );

        $referenceArray = array();
        $checkArray     = array();

        foreach ($references as $reference)
            $referenceArray[] = array(
                'id'   => $reference->id,
                'name' => $reference->name
            );

        foreach ($categories as $category)
        {
            $checkCategory = array(
                'id'     => $category->id,
                'name'   => $category->localizedName,
                'checks' => array()
            );

            foreach ($checks as $check)
                if ($check->control->check_category_id == $category->id)
                    $checkCategory['checks'][] = array(
                        'effort'    => $check->effort,
                        'reference' => $check->reference_id
                    );

            $checkArray[] = $checkCategory;
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Effort Estimation'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Effort Estimation');
		$this->render('effort', array(
            'references' => $referenceArray,
            'checks'     => $checkArray,
        ));
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
     * @param $project
     * @param $language
     * @param $model
     */
    private function _projectVulnExport($project, $language, $model) {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            'project_id' => $project->id
        ));
        $criteria->addInCondition('id', $model->targetIds);
        $criteria->order = 'host ASC';

        $targets = Target::model()->findAll($criteria);

        if (!$targets) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Targets not found.'));
            return array();
        }

        $data = array();
        $ratings = TargetCheck::getRatingNames();

        $statuses = array(
            TargetCheck::STATUS_VULN_OPEN => Yii::t('app', 'Open'),
            TargetCheck::STATUS_VULN_RESOLVED => Yii::t('app', 'Resolved'),
        );

        foreach ($targets as $target) {
            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            if (!$references) {
                continue;
            }

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            // get all categories
            $categories = TargetCheckCategory::model()->with('category')->findAllByAttributes(
                array( 'target_id' => $target->id  ),
                array( 'order'     => 'category.name ASC' )
            );

            if (!$categories) {
                continue;
            }

            foreach ($categories as $category) {
                // get all controls
                $controls = CheckControl::model()->findAllByAttributes(
                    array( 'check_category_id' => $category->check_category_id ),
                    array( 'order'             => 't.sort_order ASC' )
                );

                if (!$controls) {
                    continue;
                }

                foreach ($controls as $control) {
                    $criteria = new CDbCriteria();
                    $criteria->order = 't.sort_order ASC, tcs.id ASC';
                    $criteria->addInCondition('t.reference_id', $referenceIds);
                    $criteria->addColumnCondition(array(
                        't.check_control_id' => $control->id
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
                            )
                        ),
                        "_reference"
                    ))->findAll($criteria);

                    $criteria = new CDbCriteria();
                    $criteria->addCondition("target_id = :target_id");
                    $criteria->addCondition("rating != :hidden");
                    $criteria->addCondition("check_control_id = :check_control_id");
                    $criteria->params = array(
                        'target_id' => $target->id,
                        'hidden' => TargetCustomCheck::RATING_HIDDEN,
                        'check_control_id' => $control->id
                    );

                    $customChecks = TargetCustomCheck::model()->findAll($criteria);

                    if (!$checks && !$customChecks) {
                        continue;
                    }

                    foreach ($checks as $check) {
                        $ctr = 0;

                        foreach ($check->targetChecks as $tc) {
                            $row = $this->_getVulnExportRow(array(
                                'type'      => 'check',
                                'check'     => $tc,
                                'model'     => $model,
                                'target'    => $target,
                                'ctr'       => $ctr,
                                'ratings'   => $ratings,
                                'statuses'  => $statuses,
                            ));

                            if (!$row) {
                                continue;
                            }

                            $data[] = $row;
                            $ctr++;
                        }
                    }

                    foreach ($customChecks as $cc) {
                        $row = $this->_getVulnExportRow(array(
                            'type'      => 'custom',
                            'check'     => $cc,
                            'model'     => $model,
                            'target'    => $target,
                            'ratings'   => $ratings,
                            'statuses'  => $statuses,
                        ));

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
        $type = $data['type'];
        $check = $data['check'];
        $model = $data['model'];
        $target = $data['target'];
        $ratings = $data['ratings'];
        $statuses = $data['statuses'];
        $ctr = null;

        if ($type == TargetCheck::TYPE) {
            $ctr = $data['ctr'];
        }

        if (!in_array($check->rating, $model->ratings)) {
            return null;
        }

        $row = array();

        if (in_array(TargetCheck::COLUMN_TARGET, $model->columns)) {
            $row[TargetCheck::COLUMN_TARGET] = $target->hostPort;
        }

        if (in_array(TargetCheck::COLUMN_NAME, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_NAME] = $check->check->localizedName . ($ctr > 0 ? " " . ($ctr + 1) : "");
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_NAME] = $check->name ? $check->name : 'CUSTOM-CHECK-' . $check->reference;
            }
        }

        if (in_array(TargetCheck::COLUMN_REFERENCE, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_REFERENCE] = $check->check->_reference->name .
                    ($check->check->reference_code ? '-' . $check->check->reference_code : '');
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_REFERENCE] = "CUSTOM-CHECK-" . $check->reference;
            }
        }


        if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_BACKGROUND_INFO] = $this->_prepareVulnExportText($check->check->backgroundInfo);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_BACKGROUND_INFO] = $check->background_info;
            }
        }

        if (in_array(TargetCheck::COLUMN_QUESTION, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_QUESTION] = $this->_prepareVulnExportText($check->check->question);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_QUESTION] = $check->question;
            }
        }

        if (in_array(TargetCheck::COLUMN_RESULT, $model->columns)) {
            $row[TargetCheck::COLUMN_RESULT] = $check->result;
        }


        if (in_array(TargetCheck::COLUMN_SOLUTION, $model->columns)) {
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

        if (in_array(TargetCheck::COLUMN_ASSIGNED_USER, $model->columns)) {
            $user = $check->vulnUser ? $check->vulnUser : null;

            if ($user) {
                $row[TargetCheck::COLUMN_ASSIGNED_USER] = $user->name ? $user->name : $user->email;
            } else {
                $row[TargetCheck::COLUMN_ASSIGNED_USER] = '';
            }
        }

        if (in_array(TargetCheck::COLUMN_RATING, $model->columns)) {
            $row[TargetCheck::COLUMN_RATING] = $ratings[$check->rating];
        }

        if (in_array(TargetCheck::COLUMN_STATUS, $model->columns)) {
            $row[TargetCheck::COLUMN_STATUS] = $statuses[$check->vuln_status ? $check->vuln_status : TargetCheck::STATUS_VULN_OPEN];
        }

        return $row;
    }

    /**
     * Generate vulnerabilities report.
     */
    private function _generateVulnExportReport($model) {
        $project = Project::model()->findByPk($model->projectId);

        if (!$project) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
            return;
        }

        if (!$project->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $data = $this->_projectVulnExport($project, $language, $model);

        if (!$data) {
            return;
        }

        $header = array();

        if ($model->header) {
            if (in_array(TargetCheck::COLUMN_TARGET, $model->columns)) {
                $header[TargetCheck::COLUMN_TARGET] = Yii::t('app', 'Target');
            }

            if (in_array(TargetCheck::COLUMN_NAME, $model->columns)) {
                $header[TargetCheck::COLUMN_NAME] = Yii::t('app', 'Name');
            }

            if (in_array(TargetCheck::COLUMN_REFERENCE, $model->columns)) {
                $header[TargetCheck::COLUMN_REFERENCE] = Yii::t('app', 'Reference');
            }

            $biField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_BACKGROUND_INFO]);

            if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $model->columns)) {
                $header[TargetCheck::COLUMN_BACKGROUND_INFO] = $biField->localizedTitle;
            }

            $qField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_QUESTION]);

            if (in_array(TargetCheck::COLUMN_QUESTION, $model->columns)) {
                $header[TargetCheck::COLUMN_QUESTION] = $qField->localizedTitle;
            }

            $rField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_RESULT]);

            if (in_array(TargetCheck::COLUMN_RESULT, $model->columns)) {
                $header[TargetCheck::COLUMN_RESULT] = $rField->localizedTitle;
            }

            if (in_array(TargetCheck::COLUMN_SOLUTION, $model->columns)) {
                $header[TargetCheck::COLUMN_SOLUTION] = Yii::t('app', 'Solution');
            }

            if (in_array(TargetCheck::COLUMN_ASSIGNED_USER, $model->columns)) {
                $header[TargetCheck::COLUMN_ASSIGNED_USER] = Yii::t('app', 'Assigned');
            }

            if (in_array(TargetCheck::COLUMN_RATING, $model->columns)) {
                $header[TargetCheck::COLUMN_RATING] = Yii::t('app', 'Rating');
            }

            if (in_array(TargetCheck::COLUMN_STATUS, $model->columns)) {
                $header[TargetCheck::COLUMN_STATUS] = Yii::t('app', 'Status');
            }
        }

        // include all PHPExcel libraries
        Yii::setPathOfAlias('xls', Yii::app()->basePath . '/extensions/PHPExcel');
        Yii::import('xls.PHPExcel.Shared.ZipStreamWrapper', true);
        Yii::import('xls.PHPExcel.Shared.String', true);
        Yii::import('xls.PHPExcel', true);
        Yii::registerAutoloader(array( 'PHPExcel_Autoloader', 'Load' ), true);

        $title = Yii::t('app', '{project} Vulnerability Export', array(
            '{project}' => $project->name . ' (' . $project->year . ')'
        )) . ' - ' . date('Y-m-d');

        $xl = new PHPExcel();

        $xl->getDefaultStyle()->getFont()->setName('Helvetica');
        $xl->getDefaultStyle()->getFont()->setSize(12);
        $xl->getProperties()->setTitle($title);
        $xl->setActiveSheetIndex(0);

        $sheet = $xl->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(30);

        $row  = 1;
        $cols = range('A', 'Z');

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
        $range = 'A1:' . $lastCol . '1';

        $sheet
            ->getStyle($range)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

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

        $fileName = $title . '.xlsx';

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        ob_clean();
        flush();

        $writer = PHPExcel_IOFactory::createWriter($xl, 'Excel2007');
        $writer->save('php://output');

        exit();
    }

    /**
     * Show vulnerability export report form.
     */
    public function actionVulnExport() {
        $model = new VulnExportReportForm();

        if (isset($_POST['VulnExportReportForm'])) {
            $model->attributes = $_POST['VulnExportReportForm'];
            $model->header = isset($_POST['VulnExportReportForm']['header']);

            if ($model->validate()) {
                $this->_generateVulnExportReport($model);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $bInfoField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_BACKGROUND_INFO]);
        $questionField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_QUESTION]);
        $resultField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_RESULT]);

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Export'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Vulnerability Export');
		$this->render('vuln_export', array(
            'model' => $model,
            'clients' => $clients,
            'ratings' => TargetCheck::getRatingNames(),
            'columns' => array(
                TargetCheck::COLUMN_TARGET => Yii::t('app', 'Target'),
                TargetCheck::COLUMN_NAME => Yii::t('app', 'Name'),
                TargetCheck::COLUMN_REFERENCE => Yii::t('app', 'Reference'),
                TargetCheck::COLUMN_BACKGROUND_INFO => $bInfoField->localizedTitle,
                TargetCheck::COLUMN_QUESTION => $questionField->localizedTitle,
                TargetCheck::COLUMN_RESULT => $resultField->localizedTitle,
                TargetCheck::COLUMN_SOLUTION => Yii::t('app', 'Solution'),
                TargetCheck::COLUMN_RATING => Yii::t('app', 'Rating'),
                TargetCheck::COLUMN_ASSIGNED_USER => Yii::t('app', 'Assigned'),
                TargetCheck::COLUMN_STATUS => Yii::t('app', 'Status'),
            )
        ));
    }

    /**
     * Report project tracked time
     * @param $id
     */
    public function actionTrackedTime($id) {
        $projectId = (int) $id;
        $this->_generateTrackedTimeReport($projectId);
    }

    /**
     * Generates project tracked time report
     * @param $id
     */
    private function _generateTrackedTimeReport($id) {
        $projectId = (int) $id;
        $project = Project::model()->findByPk($projectId);

        if ($project === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
            return;
        }

        if (!$project->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $records = $project->timeRecords;

        $fileName = Yii::t('app', 'Time Tracking Report') . ' - ' . $project->name . ' (' . $project->year . ').rtf';

        $r = new RtfReport();
        $r->setup();
        $section = $r->rtf->addSection();

        // main title
        $section->writeText(Yii::t("app", "Time Tracking Report"), $r->h2Font, $r->centerTitlePar);
        $section->writeText(" ", $r->h2Font, $r->noPar);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_CENTER);

        $table->addColumnsList(array($r->docWidth * 0.3, $r->docWidth * 0.5, $r->docWidth * 0.2));
        $table->addRows(count($records) + 1);

        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
        $table->setFontForCellRange($r->boldFont, 1, 1, 1, 3);
        $table->setFontForCellRange($r->textFont, 2, 1, count($records) + 1, 3);
        $table->setBorderForCellRange($r->thinBorder, 1, 1, count($records) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($records) + 1; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                $table->getCell($row, $col)->setCellPaddings($r->cellPadding, $r->cellPadding, $r->cellPadding, $r->cellPadding);

                if ($row > 1) {
                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                } else {
                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                }
            }
        }

        $row = 1;

        $table->getCell($row, 1)->writeText(Yii::t('app', 'Time Added'));
        $table->getCell($row, 2)->writeText(Yii::t('app', 'Description'));
        $table->getCell($row, 3)->writeText(Yii::t('app', 'Time Logged'));

        $row++;

        // filling positions list
        foreach ($records as $record) {
            $table->getCell($row, 1)->setCellPaddings(
                $r->cellPadding,
                $r->cellPadding,
                $r->cellPadding,
                $r->cellPadding
            );
            $table->getCell($row, 2)->setCellPaddings(
                $r->cellPadding,
                $r->cellPadding,
                $r->cellPadding,
                $r->cellPadding
            );
            $table->getCell($row, 1)->writeText(DateTimeFormat::toISO($record->create_time), $r->textFont, $r->noPar);
            $table->getCell($row, 2)->writeText($record->description, $r->textFont, $r->noPar);
            $table->getCell($row, 3)->writeText($record->hours . ' h', $r->textFont, $r->noPar);
            $row++;
        }

        // total hours
        $section->writeText(Yii::t("app", "Summary") . ': ' . $project->trackedTime . ' h', $r->h3Font, $r->rightPar);

        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['reports']['tmpFilesPath'] . '/' . $hashName;
        $r->rtf->save($filePath);

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }
}
