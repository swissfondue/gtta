<?php

/**
 * Report controller.
 */
class ReportController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "showReports",
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

        $prm = new ReportManager();
        $data = $prm->getProjectReportData($targetIds, $templateCategoryIds, $project, $fields, $language);
        $data = array_merge($data, [
            "template" => $template,
            "pageMargin" => $form->pageMargin,
            "cellPadding" => $form->cellPadding,
            "fontSize" => $form->fontSize,
            "fontFamily" => $form->fontFamily,
            "fileType" => $form->fileType,
            "risk" => [
                "template" => $form->riskTemplateId,
                "matrix" => [],
            ],
            "options" => $options,
            "infoLocation" => $form->infoChecksLocation,
        ]);

        $plugin = ReportPlugin::getPlugin($template, $data, $language);
        $plugin->generate();
        $plugin->sendOverHttp();

        exit();
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
                ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE => Yii::t("app", "in a separate table"),
                ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION => Yii::t("app", "in a separate section"),
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
     * Sort controls.
     */
    public static function sortControls($a, $b) {
        return $a['degree'] > $b['degree'];
    }

    /**
     * Show degree of fulfillment report form.
     */
    public function actionFulfillment() {
        $form = new FulfillmentDegreeForm();

        if (isset($_POST['FulfillmentDegreeForm'])) {
            $form->attributes = $_POST['FulfillmentDegreeForm'];

            if ($form->validate()) {
                $project = Project::model()->findByAttributes(array(
                    "client_id" => $data["clientId"],
                    "id" => $data["projectId"],
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

                $r = new RtfReport();
                $r->generateFulfillmentDegreeReport($form);
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
     * Show risk matrix report form.
     */
    public function actionRiskMatrix() {
        $form = new RiskMatrixForm();

        if (isset($_POST['RiskMatrixForm'])) {
            $form->attributes = $_POST['RiskMatrixForm'];

            if ($form->validate()) {
                $r = new RtfReport();
                $r->generateRiskMatrixReport($form);
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
