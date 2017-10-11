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
     * Docx project form
     * @param $id
     */
    public function actionProjectDocx($id) {
        $project = $this->_getProject($id);
        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        /** @var ReportTemplate $template */
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
        ))->findByPk($project->report_template_id);

        if (!$template || $template->type != ReportTemplate::TYPE_DOCX) {
            $this->redirect(["projectReport/template", "id" => $project->id]);
        }

        $form = new ProjectReportForm(ProjectReportForm::SCENARIO_DOCX);

        if (isset($_POST["ProjectReportForm"])) {
            $form->attributes = $_POST["ProjectReportForm"];

            try {
                if (!$form->validate()) {
                    throw new Exception();
                }

                $templateCategoryIds = array();

                foreach ($template->vulnSections as $section) {
                    $templateCategoryIds[] = $section->check_category_id;
                }

                $fields = [];

                /** @var GlobalCheckField $f */
                foreach (GlobalCheckField::model()->findAll() as $f) {
                    $fields[] = $f->name;
                }

                $prm = new ReportManager();
                $data = $prm->getProjectReportData($form->targetIds, $templateCategoryIds, $project, $fields, $language);
                $plugin = ReportPlugin::getPlugin($template, $data, $language);

                try {
                    $plugin->generate();
                    $plugin->sendOverHttp();
                } catch (Exception $e) {
                    Yii::log($e->getMessage() . "\n" . $e->getTraceAsString(), CLogger::LEVEL_ERROR);
                    Yii::app()->user->setFlash("error", Yii::t("app", "Error generating report."));
                }
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $targets = Target::model()->findAllByAttributes(
            ["project_id" => $project->id],
            ["order" => "host"]
        );

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Report"), ""];

        // display the report generation form
        $this->pageTitle = $project->name;
		$this->render("//project/report/project/docx", [
            "project" => $project,
            "form" => $form,
            "template" => $template,
            "targets" => $targets,
        ]);
    }

    /**
     * Rtf project form
     * @param $id
     */
    public function actionProjectRtf($id) {
        $project = $this->_getProject($id);
        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        /** @var ReportTemplate $template */
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
        ))->findByPk($project->report_template_id);

        if (!$template || $template->type != ReportTemplate::TYPE_RTF) {
            $this->redirect(["projectReport/template", "id" => $project->id]);
        }

        $form = new ProjectReportForm();
        $form->fromJSON($project->report_options);

        if (!$project->report_options) {
            $form->title = true;
        }

        if (isset($_POST["ProjectReportForm"])) {
            $form->attributes = $_POST["ProjectReportForm"];

            try {

                if (!$form->validate()) {
                    throw new FormValidationException();
                }

                if (!$form->fields) {
                    $form->fields = [];
                }

                if (!$form->title) {
                    $form->title = [];
                }

                $templateCategoryIds = array();

                foreach ($template->vulnSections as $section) {
                    $templateCategoryIds[] = $section->check_category_id;
                }

                $riskTemplate = null;

                if ($form->riskTemplateId) {
                    $riskTemplate = RiskTemplate::model()->findByPk($form->riskTemplateId);
                }

                if (
                    $template->hasSection(ReportSection::TYPE_RISK_MATRIX) &&
                    !$riskTemplate
                ) {
                    $form->addError("riskTemplateId", Yii::t("app", "Risk Matrix Template is required."));
                    throw new FormValidationException();
                }

                $project->report_options = $form->toJSON();
                $project->save();

                $sections = $template->sections;

                if ($project->custom_report) {
                    $sections = ProjectReportSection::model()->findAllByAttributes(
                        ["project_id" => $project->id],
                        ["order" => "sort_order ASC"]
                    );
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
                        "matrix" => $form->riskMatrix,
                    ],
                    "title" => $form->title,
                    "infoLocation" => $form->infoChecksLocation,
                    "fields" => $form->fields,
                    "sections" => $sections,
                ]);

                $plugin = ReportPlugin::getPlugin($template, $data, $language);
                $plugin->generate();
                $plugin->sendOverHttp();
            } catch (FormValidationException $e) {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            } catch (Exception $e) {
                Yii::log($e->getMessage() . "\n" . $e->getTraceAsString(), CLogger::LEVEL_ERROR);
                Yii::app()->user->setFlash("error", Yii::t("app", "Error generating report. ".$e->getMessage()));
            }
        }

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
        $targets = Target::model()->findAllByAttributes(
            ["project_id" => $project->id],
            ["order" => "host"]
        );

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Report"), ""];

        // display the report generation form
        $this->pageTitle = $project->name;
		$this->render("//project/report/project/rtf", [
            "project" => $project,
            "form" => $form,
            "riskTemplates" => $riskTemplates,
            "fields" => $fields,
            "infoChecksLocation" => [
                ProjectReportForm::INFO_LOCATION_TARGET => Yii::t("app", "Vulnerability List Section"),
                ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE => Yii::t("app", "Vulnerability List Section (separate table)"),
                ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION => Yii::t("app", "Info Checks Section"),
            ],
            "template" => $template,
            "targets" => $targets,
            "fileTypes" => [
                ProjectReportForm::FILE_TYPE_RTF => Yii::t("app", "RTF"),
                ProjectReportForm::FILE_TYPE_ZIP => Yii::t("app", "RTF + Attachments"),
            ]
        ]);
    }

    /**
     * Show project report form.
     * @param int $id
     */
    public function actionProject($id) {
        $project = $this->_getProject($id);

        if (!$project->report_template_id) {
            $this->redirect(["projectReport/template", "id" => $project->id]);
        }

        /** @var ReportTemplate $template */
        $template = ReportTemplate::model()->findByPk($project->report_template_id);

        if (!$template) {
            $this->redirect(["projectReport/template", "id" => $project->id]);
        }

        switch ($template->type) {
            case ReportTemplate::TYPE_DOCX:
                $this->redirect(["projectReport/projectDocx", "id" => $project->id]);
                break;

            case ReportTemplate::TYPE_RTF:
                $this->redirect(["projectReport/projectRtf", "id" => $project->id]);
                break;
        }

        $this->redirect(["projectReport/template", "id" => $project->id]);
    }

    /**
     * Report template selection
     * @param $id
     */
    public function actionTemplate($id) {
        $project = $this->_getProject($id);
        $template = ReportTemplate::model()->findByPk($project->report_template_id);

        $form = new ProjectReportTemplateForm();
        $form->fromModel($project);

        if (isset($_POST["ProjectReportTemplateForm"])) {
            $form->attributes = $_POST["ProjectReportTemplateForm"];

            try {
                if (!$form->validate()) {
                    throw new Exception();
                }

                $previousTemplate = $project->report_template_id;
                $previousCustom = $project->custom_report;

                $form->customReport = isset($_POST["ProjectReportTemplateForm"]["customReport"]);
                $project->fromForm($form);

                /** @var ReportTemplate $template */
                $template = ReportTemplate::model()->findByPk($project->report_template_id);

                if ($template->type != ReportTemplate::TYPE_RTF) {
                    $project->custom_report = false;
                }

                $project->save();

                // copy sections to project
                if (
                    $project->custom_report &&
                    ($project->custom_report != $previousCustom || $project->report_template_id != $previousTemplate)
                ) {
                    $pm = new ProjectManager();
                    $pm->initCustomReportTemplate($project, $template);
                }

                $this->redirect(["projectReport/project", "id" => $project->id]);
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }


        $criteria = new CDbCriteria();
        $criteria->order = "COALESCE(l10n.name, t.name) ASC";
        $criteria->together = true;

        $templates = ReportTemplate::model()->with([
            "sections",
            "l10n" => [
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => ["language_id" => $language]
            ]
        ])->findAll($criteria);

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Report"), ""];

        $this->pageTitle = $project->name;
		$this->render("//project/report/project/template", [
            "project" => $project,
            "form" => $form,
            "templates" => $templates,
            "template" => $template,
        ]);
    }

    /**
     * Custom sections action
     * @param $id
     * @throws CHttpException
     */
    public function actionSections($id) {
        $project = $this->_getProject($id);

        $sections = ProjectReportSection::model()->findAllByAttributes(
            ["project_id" => $project->id],
            ["order" => "sort_order ASC"]
        );

        $this->breadcrumbs[] = [Yii::t("app", "Projects"), $this->createUrl("project/index")];
        $this->breadcrumbs[] = [$project->name, $this->createUrl("project/view", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Report"), $this->createUrl("projectReport/projectRtf", ["id" => $project->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Customization"), ""];

        $this->pageTitle = $project->name;
        $this->render("//project/report/project/sections", array(
            "project" => $project,
            "sections" => $sections,
            "languages" => Language::model()->findAll(),
            "system" => System::model()->findByPk(1)
        ));
    }

    /**
     * Section save
     * @param $id
     */
    public function actionSaveSection($id) {
        $response = new AjaxResponse();

        try {
            $project = $this->_getProject($id);

            $form = new ProjectReportSectionEditForm(ProjectReportSectionEditForm::SCENARIO_SECTION);
            $form->attributes = $_POST["ProjectReportSectionEditForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            /** @var ProjectReportSection $section */
            $section = null;

            if ($form->id) {
                $section = ProjectReportSection::model()->findByAttributes([
                    "id" => $form->id,
                    "project_id" => $project->id,
                ]);
            } else {
                $form->id = null;
                $section = new ProjectReportSection();
                $section->project_id = $project->id;
                $section->sort_order = 0;
            }

            if (!$section) {
                throw new Exception(Yii::t("app", "Section not found."));
            }

            $section->fromForm($form);
            $section->sort_order = array_search($section->id ? $section->id : null, $form->order);

            if (!$section->sort_order) {
                $section->sort_order = 0;
            }

            $section->save();

            // save orders
            foreach ($form->order as $order => $s) {
                ProjectReportSection::model()->updateAll(
                    ["sort_order" => $order],
                    "id = :id AND project_id = :p",
                    [
                        ":id" => $s,
                        ":p" => $project->id,
                    ]
                );
            }

            $response->addData("id", $section->id);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Save section order
     * @param $id
     */
    public function actionSaveSectionOrder($id) {
        $response = new AjaxResponse();

        try {
            $project = $this->_getProject($id);

            $form = new ProjectReportSectionEditForm();
            $form->attributes = $_POST["ProjectReportSectionEditForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            // save orders
            foreach ($form->order as $order => $s) {
                ProjectReportSection::model()->updateAll(
                    ["sort_order" => $order],
                    "id = :id AND project_id = :p",
                    [
                        ":id" => $s,
                        ":p" => $project->id,
                    ]
                );
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control report section.
     * @param $id
     */
    public function actionControlSection($id) {
        $response = new AjaxResponse();

        try {
            $project = $this->_getProject($id);

            $form = new EntryControlForm();
            $form->attributes = $_POST["EntryControlForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            /** @var ProjectReportSection $section */
            $section = ProjectReportSection::model()->findByAttributes([
                "id" => $form->id,
                "project_id" => $project->id,
            ]);

            if (!$section) {
                throw new Exception(Yii::t("app", "Section not found."));
            }

            switch ($form->operation) {
                case "delete":
                    $section->delete();
                    break;

                default:
                    throw new Exception(Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
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