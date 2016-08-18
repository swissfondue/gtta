<?php

/**
 * Project report controller
 */
class ProjectReportController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return [
            "https",
			"checkAuth",
            "showReports",
            "idle",
		];
	}

    /**
     * Get project
     * @param int $id
     * @return Project
     * @throws CHttpException
     */
    private function _getProject($id) {
        $project = Project::model()->findByPk($id);

        if (!$project || !$project->checkPermission()) {
            throw new CHttpException(404, Yii::t("app", "Project not found."));
        }

        return $project;
    }

    /**
     * Show project report form.
     * @param int $id
     */
    public function actionProject($id) {
        $project = $this->_getProject($id);
        $form = new ProjectReportForm();

        if (isset($_POST["ProjectReportForm"])) {
            $form->attributes = $_POST["ProjectReportForm"];

            if ($form->validate()) {
                if (!$form->fields) {
                    $form->fields = [];
                }

                if (!$form->options) {
                    $form->options = [];
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
                ))->findByPk($form->templateId);

                if ($template === null) {
                    Yii::app()->user->setFlash("error", Yii::t("app", "Template not found."));
                    return;
                }

                $templateCategoryIds = array();

                foreach ($template->vulnSections as $section) {
                    $templateCategoryIds[] = $section->check_category_id;
                }

                $riskTemplate = null;

                if ($form->riskTemplateId) {
                    $riskTemplate = RiskTemplate::model()->findByPk($form->riskTemplateId);
                }

                $prm = new ReportManager();
                $data = $prm->getProjectReportData($form->targetIds, $templateCategoryIds, $project, $form->fields, $language);
                $data = array_merge($data, [
                    "template" => $template,
                    "pageMargin" => $form->pageMargin,
                    "cellPadding" => $form->cellPadding,
                    "fontSize" => $form->fontSize,
                    "fontFamily" => $form->fontFamily,
                    "fileType" => $form->fileType,
                    "risk" => [
                        "template" => $riskTemplate,
                        "matrix" => [],
                    ],
                    "options" => $form->options,
                    "infoLocation" => $form->infoChecksLocation,
                ]);

                $plugin = ReportPlugin::getPlugin($template, $data, $language);

                try {
                    $plugin->generate();
                    $plugin->sendOverHttp();
                } catch (Exception $e) {
                    Yii::log($e->getMessage() . "\n" . $e->getTraceAsString(), CLogger::LEVEL_ERROR);
                    Yii::app()->user->setFlash("error", Yii::t("app", "Error generating report."));
                }
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

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Report"), ""];

        // display the report generation form
        $this->pageTitle = $project->name;
		$this->render("//project/report/project", [
            "project" => $project,
            "model" => $form,
            "clients" => $clients,
            "templates" => $templates,
            "riskTemplates" => $riskTemplates,
            "fields" => $fields,
            "infoChecksLocation" => [
                ProjectReportForm::INFO_LOCATION_TARGET => Yii::t("app", "Vulnerability List Section"),
                ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE => Yii::t("app", "Vulnerability List Section (separate table)"),
                ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION => Yii::t("app", "Info Checks Section"),
            ],
        ]);
    }

    /**
     * Show comparison report form.
     * @param int $id
     */
    public function actionComparison($id) {
        $project = $this->_getProject($id);
        $form = new ProjectComparisonForm();

        if (isset($_POST["ProjectComparisonForm"])) {
            $form->attributes = $_POST["ProjectComparisonForm"];

            if ($form->validate()) {
                /** @var Project $p */
                $p = Project::model()->findByPk($form->projectId);

                $r = new RtfReport();
                $r->setup($form->pageMargin, $form->cellPadding, $form->fontSize, $form->fontSize);
                $r->generateComparisonReport($project, $p);
                $r->sendOverHttp();
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

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Comparison"), ""];

        // display the report generation form
        $this->pageTitle = $project->name;
		$this->render("//project/report/comparison", array(
            "project" => $project,
            "model" => $form,
            "clients" => $clients
        ));
    }

    /**
     * Show degree of fulfillment report form.
     * @param int $id
     */
    public function actionFulfillment($id) {
        $project = $this->_getProject($id);
        $form = new FulfillmentDegreeForm();

        if (isset($_POST["FulfillmentDegreeForm"])) {
            $form->attributes = $_POST["FulfillmentDegreeForm"];

            if ($form->validate()) {
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(["project_id" => $project->id]);
                $criteria->addInCondition("id", $form->targetIds);
                $targets = Target::model()->findAll($criteria);

                $r = new RtfReport();
                $r->setup($form->pageMargin, $form->cellPadding, $form->fontSize, $form->fontSize);
                $r->generateFulfillmentDegreeReport($project, $targets);
                $r->sendOverHttp();
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

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Degree of Fulfillment"), ""];

        // display the report generation form
        $this->pageTitle = $project->name;
		$this->render("//project/report/fulfillment", array(
            "project" => $project,
            "model" => $form,
            "clients" => $clients
        ));
    }

    /**
     * Show risk matrix report form.
     * @param int $id
     */
    public function actionRiskMatrix($id) {
        $project = $this->_getProject($id);
        $form = new RiskMatrixForm();

        if (isset($_POST["RiskMatrixForm"])) {
            $form->attributes = $_POST["RiskMatrixForm"];

            if ($form->validate()) {
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(["project_id" => $project->id]);
                $criteria->addInCondition("id", $form->targetIds);
                $targets = Target::model()->findAll($criteria);

                /** @var RiskTemplate $template */
                $template = RiskTemplate::model()->findByAttributes(array(
                    "id" => $form->templateId
                ));

                if ($template === null) {
                    Yii::app()->user->setFlash("error", Yii::t("app", "Template not found."));
                    return;
                }

                $r = new RtfReport();
                $r->setup($form->pageMargin, $form->cellPadding, $form->fontSize, $form->fontSize);
                $r->generateRiskMatrixReport($project, $targets, $template, $form->matrix);
                $r->sendOverHttp();
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

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Risk Matrix"), ""];

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $templates = RiskTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on"       => "language_id = :language_id",
                "params"   => array("language_id" => $language)
            )
        ))->findAllByAttributes(
            array(),
            array("order" => "COALESCE(l10n.name, t.name) ASC")
        );

        // display the report generation form
        $this->pageTitle = $project->name;
		$this->render("//project/report/risk-matrix", array(
            "project" => $project,
            "model" => $form,
            "clients" => $clients,
            "templates" => $templates
        ));
    }

    /**
     * Show vulnerability export report form.
     * @param int $id
     */
    public function actionVulnExport($id) {
        $project = $this->_getProject($id);
        $form = new VulnExportReportForm();

        if (isset($_POST["VulnExportReportForm"])) {
            $form->attributes = $_POST["VulnExportReportForm"];
            $form->header = isset($_POST["VulnExportReportForm"]["header"]);

            if ($form->validate()) {
                $language = Language::model()->findByAttributes([
                    "code" => Yii::app()->language
                ]);

                if ($language) {
                    $language = $language->id;
                }

                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(["project_id" => $project->id]);
                $criteria->addInCondition("id", $form->targetIds);
                $targets = Target::model()->findAll($criteria);

                $rm = new ReportManager();
                $rm->generateVulnExportReport($project, $targets, $language, $form->header, $form->columns, $form->ratings);
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

        $bInfoField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_BACKGROUND_INFO]);
        $questionField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_QUESTION]);
        $resultField = GlobalCheckField::model()->findByAttributes(["name" => GlobalCheckField::FIELD_RESULT]);

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Vulnerability Export"), ""];

        // display the report generation form
        $this->pageTitle = $project->name;
		$this->render("//project/report/vuln-export", array(
            "project" => $project,
            "model" => $form,
            "clients" => $clients,
            "ratings" => TargetCheck::getRatingNames(),
            "columns" => array(
                TargetCheck::COLUMN_TARGET => Yii::t("app", "Target"),
                TargetCheck::COLUMN_NAME => Yii::t("app", "Name"),
                TargetCheck::COLUMN_REFERENCE => Yii::t("app", "Reference"),
                TargetCheck::COLUMN_BACKGROUND_INFO => $bInfoField->localizedTitle,
                TargetCheck::COLUMN_QUESTION => $questionField->localizedTitle,
                TargetCheck::COLUMN_RESULT => $resultField->localizedTitle,
                TargetCheck::COLUMN_SOLUTION => Yii::t("app", "Solution"),
                TargetCheck::COLUMN_RATING => Yii::t("app", "Rating"),
                TargetCheck::COLUMN_ASSIGNED_USER => Yii::t("app", "Assigned"),
                TargetCheck::COLUMN_STATUS => Yii::t("app", "Status"),
            )
        ));
    }

    /**
     * Report project tracked time
     * @param int $id
     */
    public function actionTrackedTime($id) {
        $project = $this->_getProject($id);

        $r = new RtfReport();
        $r->setup();
        $r->generateTimeTrackingReport($project);
        $r->sendOverHttp();
    }
}