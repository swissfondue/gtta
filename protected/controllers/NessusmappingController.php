<?php

/**
 * Class NessusMappingController
 */
class NessusmappingController extends Controller {
    /**
     * @return array action filters
     */
    public function filters() {
        return [
            "https",
            "checkAuth + index, view, edit, view, searchChecks, updateVuln",
            "checkAdmin + edit, control, edit, view, updateVuln",
            "idle",
            "ajaxOnly + control, searchChecks, updateVuln",
            "postOnly + control, searchChecks, updateVuln",
        ];
    }

    /**
     * Nessus mappings list
     * @param int $page
     */
    public function actionIndex($page = 1) {
        $page = (int) $page;

        $criteria = new CDbCriteria();
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->order  = "t.created_at DESC";

        $mappings = NessusMapping::model()->findAll($criteria);
        $count = NessusMapping::model()->count($criteria);

        $paginator = new Paginator($count, $page);

        $this->pageTitle = Yii::t("app", "Nessus Mappings");
        $this->breadcrumbs[] = [Yii::t("app", "Nessus Mappings"), ""];

        $this->render("index", [
            "mappings" => $mappings,
            "p" => $paginator,
        ]);
    }

    /**
     * Control mapping
     */
    public function actionControl() {
        $response = new AjaxResponse();

            $model = new EntryControlForm();
            $model->attributes = $_POST["EntryControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id = (int) $model->id;

            $mapping = NessusMapping::model()->findByPk($id);

            if (!$mapping) {
                throw new Exception("Mapping not found.", 404);
            }

            if ($model->operation != "delete") {
                throw new CHttpException(403, Yii::t("app", "Unknown operation."));
            }

            $mapping->delete();

        echo $response->serialize();
    }

    /**
     * Mapping edit page.
     */
    public function actionEdit($id) {
        $id = (int) $id;
        $mapping = NessusMapping::model()->findByPk($id);

        if (!$mapping) {
            throw new CHttpException(404, "Mapping not found.");
        }

        $languages = Language::model()->findAll();

        $form = new NessusMappingEditForm();
        $form->localizedItems = [];

        $mappingsL10n = NessusMappingL10n::model()->findAllByAttributes(array(
            "nessus_mapping_id" => $mapping->id
        ));

        foreach ($mappingsL10n as $ml10n) {
            $form->localizedItems[$ml10n->language_id] = [
                "name" => $ml10n->name
            ];
        }

        if (isset($_POST["NessusMappingEditForm"])) {
            $form->attributes = $_POST["NessusMappingEditForm"];
            $form->name = $form->defaultL10n($languages, "name");

            if ($form->validate()) {
                $mapping->name = $form->name;

                foreach ($form->localizedItems as $languageId => $value) {
                    $mappingL10n = NessusMappingL10n::model()->findByAttributes([
                        "nessus_mapping_id" => $mapping->id,
                        "language_id" => $languageId
                    ]);

                    if (!$mappingL10n) {
                        $mappingL10n = new NessusMappingL10n();
                        $mappingL10n->nessus_mapping_id = $mapping->id;
                        $mappingL10n->language_id = $languageId;
                    }

                    if ($value["name"] == "") {
                        $value["name"] = null;
                    }

                    $mappingL10n->name = $value["name"];
                    $mappingL10n->save();
                }

                $mapping->save();

                Yii::app()->user->setFlash("success", Yii::t("app", "Mapping saved."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->breadcrumbs[] = [Yii::t("app", "Nessus Mappings"), $this->createUrl("nessusmapping/index")];
        $this->breadcrumbs[] = [$mapping->name, $this->createUrl("nessusmapping/edit", ["id" => $mapping->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Edit"), ""];
        $this->pageTitle = $mapping->name;

        $this->render("edit", [
            "form" => $form,
            "mapping" => $mapping,
            "languages" => $languages
        ]);
    }

    /**
     * View mapping of nessus mapping
     * @param $id
     */
    public function actionView($id) {
        $id = (int) $id;

        $mapping = NessusMapping::model()->findByPk($id);

        if (!$mapping) {
            throw new CHttpException(404, "Mapping not found.");
        }

        $ratings = TargetCheck::getValidRatings();
        $nessusRatings = NessusReportManager::$ratings;

        $this->breadcrumbs[] = [Yii::t("app", "Nessus Mappings"), $this->createUrl("nessusmapping/index")];
        $this->breadcrumbs[] = [$mapping->name, $this->createUrl("nessusmapping/edit", ["id" => $mapping->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Mapping"), ""];
        $this->pageTitle = $mapping->name;

        $this->render("view", [
            "mapping" => $mapping,
            "ratings" => $ratings,
            "nessusRatings" => $nessusRatings,
        ]);
    }

    /**
     * Search checks
     * @param $id
     */
    public function actionSearchChecks($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $mapping = NessusMapping::model()->findByPk($id);

            if (!$mapping) {
                throw new Exception(Yii::t("app", "Mapping not found."));
            }

            if (!isset($_POST["SearchForm"])) {
                throw new Exception(Yii::t("app", "Invalid search query."));
            }

            $form = new SearchForm();
            $form->attributes = $_POST["SearchForm"];

            if (!$form->validate()) {
                $errorText = '';

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $data = [];
            $exclude = [];

            $ctm = new CategoryManager();
            $language = Language::model()->findByAttributes([
                "code" => Yii::app()->language
            ]);

            /** @var CheckCategoryL10n[] $categories */
            $categories = $ctm->filter($form->query, $language->id);

            foreach ($categories as $categoryL10) {
                $controls = $categoryL10->category->controls;
                $checksData = [];

                foreach ($controls as $control) {
                    $checks = $control->checks;

                    foreach ($checks as $check) {
                        if (in_array($check->id, $exclude)) {
                            continue;
                        }

                        $checksData[] = $check->serialize($language->id);
                        $exclude[] = $check->id;
                    }
                }

                $item["checks"] = $checksData;
                $item["name"] = $categoryL10->name;
                $data[] = $item;
            }

            $response->addData("categories", $data);

            $cm = new CheckManager();
            $checks = $cm->filter($form->query, $language->id, $exclude);
            $data = [];

            foreach ($checks as $check) {
                $data[] = $check->check->serialize($language->id);
            }

            $response->addData("checks", $data);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Update mapping Vulnerability
     */
    public function actionUpdateVuln() {
        $response = new AjaxResponse();

        try {
            if (!isset($_POST["NessusMappingVulnUpdateForm"])) {
                throw new Exception(Yii::t("app", "No update data."), 400);
            }

            $form = new NessusMappingVulnUpdateForm();
            $form->attributes = $_POST["NessusMappingVulnUpdateForm"];

            if (!$form->validate()) {
                $errorText = '';

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $vuln = NessusMappingVuln::model()->findByPk($form->vulnId);

            if (!$vuln) {
                throw new Exception("Mapping item not found.", 404);
            }

            $transaction = NessusMappingVuln::model()->dbConnection->beginTransaction();

            try {
                $vuln->check_id = null;
                $vuln->check_result_id = null;
                $vuln->check_solution_id = null;
                $vuln->rating = null;
                $vuln->save();

                if ($form->checkId) {
                    $check = Check::model()->findByPk($form->checkId);

                    if (!$check) {
                        throw new Exception("Check not found.", 404);
                    }

                    $vuln->check_id = $check->id;

                    if ($form->resultId) {
                        $result = CheckResult::model()->findByPk($form->resultId);

                        if ($result && $result->check->id == $check->id) {
                            $vuln->check_result_id = $result->id;
                        }
                    }

                    if ($form->solutionId) {
                        $solution = CheckSolution::model()->findByPk($form->solutionId);

                        if ($solution && $solution->check->id == $check->id) {
                            $vuln->check_solution_id = $solution->id;
                        }
                    }

                    if ($form->rating) {
                        $vuln->rating = $form->rating;
                    }
                }

                $vuln->active = $form->active;
                $vuln->insert_nessus_title = (bool) $form->insertTitle;
                $vuln->save();

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }

            $renderController = new CController("RenderController");
            $renderedVuln = $renderController->renderPartial("/layouts/partial/mapping/mapping-item", [
                "vuln" => $vuln,
                "ratings" => TargetCheck::getValidRatings()
            ], true);

            $response->addData("item_rendered", $renderedVuln);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Filter mapping items
     */
    public function actionFilterVulns() {
        $response = new AjaxResponse();

        try {
            if (!isset($_POST["NessusMappingVulnFilterForm"])) {
                throw new Exception(Yii::t("app", "No mapping vulns found."), 400);
            }

            $form = new NessusMappingVulnFilterForm();
            $form->attributes = $_POST["NessusMappingVulnFilterForm"];

            if (!$form->validate()) {
                $errorText = '';

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }
            $sortBy = "checks.name";
            $sortDirection = "ASC";

            switch ($form->sortDirection) {
                case NessusMapping::FILTER_SORT_ASCENDING:
                    $sortDirection = "ASC";
                    break;

                case NessusMapping::FILTER_SORT_DESCENDING:
                    $sortDirection = "DESC";
                    break;
            }

            switch ($form->sortBy) {
                case NessusMapping::FILTER_SORT_ISSUE:
                    $sortBy = "nessus_plugin_name";
                    break;

                case NessusMapping::FILTER_SORT_RATING:
                    $sortBy = "rating";
                    break;

                case NessusMapping::FILTER_SORT_CHECK:
                    $sortBy = "checks.name";
                    break;
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition("nessus_mapping_id = :mapping_id");
            $criteria->params = ["mapping_id" => $form->mappingId];
            $criteria->addInCondition("nessus_host", $form->hosts);
            $criteria->addInCondition("nessus_rating", $form->ratings);
            $criteria->join = "LEFT JOIN checks ON checks.id = check_id";
            $criteria->order = $sortBy . " " . $sortDirection;

            $vulns = NessusMappingVuln::model()->findAll($criteria);

            $ratings = TargetCheck::getValidRatings();
            $table = $this->renderPartial("/layouts/partial/mapping/mapping-table", [
                "vulns" => $vulns,
                "ratings" => $ratings
            ], 1);

            $response->addData("table_rendered", $table);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Activate vulns
     */
    public function actionActivate() {
        $response = new AjaxResponse();

        try {
            if (!isset($_POST["NessusMappingVulnActivateForm"])) {
                throw new Exception(Yii::t("app", "No mapping vulns found."), 400);
            }

            $form = new NessusMappingVulnActivateForm();
            $form->attributes = $_POST["NessusMappingVulnActivateForm"];

            if (!$form->validate()) {
                $errorText = '';

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition("id", $form->mappingIds);

            NessusMappingVuln::model()->updateAll([
                "active" => $form->activate
            ], $criteria);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}