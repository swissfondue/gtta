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
            "checkAuth + index, view, edit, view",
            "checkAdmin + edit, control, edit, view",
            "idle",
            "ajaxOnly + control",
            "postOnly + control",
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

        $this->breadcrumbs[] = [Yii::t("app", "Nessus Mappings"), $this->createUrl("nessusmapping/index")];
        $this->breadcrumbs[] = [$mapping->name, $this->createUrl("nessusmapping/edit", ["id" => $mapping->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Mapping"), ""];
        $this->pageTitle = $mapping->name;

        $this->render("view", [
            "mapping" => $mapping,
            "ratings" => $ratings
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

            $cm = new CheckManager();
            $language = Language::model()->findByAttributes([
                "code" => Yii::app()->language
            ]);
            $mappingVulns = NessusMappingVuln::model()->findAllByAttributes([
                "nessus_mapping_id" => $mapping->id
            ]);
            $exclude = [];

            foreach ($mappingVulns as $mv) {
                if (isset($mv->check)) {
                    $exclude[] = $mv->check->id;
                }
            }

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
}