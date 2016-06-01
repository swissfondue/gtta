<?php

/**
 * Customization controller.
 */
class CustomizationController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            "https",
            "checkAuth",
            "checkAdmin",
            "ajaxOnly + control, controlcontrol, controlcheck, controlresult, controlsolution, controlinput, controlscript",
            "postOnly + control, controlcontrol, controlcheck, controlresult, controlsolution, controlinput, controlscript"
        );
    }

    /**
     * Categories list
     */
    public function actionIndex() {
        $this->breadcrumbs[] = array(Yii::t("app", "Customization"), "");

        $this->pageTitle = Yii::t("app", "Customization");
        $this->render("index", []);
    }

    /**
     * Checks customization category list
     */
    public function actionChecks() {
        $this->breadcrumbs[] = array(Yii::t("app", "Customization"), $this->createUrl("customization/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Checks"), "");

        $this->pageTitle = Yii::t("app", "Checks");
        $this->render("check/index", []);
    }

    /**
     * Check fields list
     * @param int $page
     * @throws CHttpException
     */
    public function actionCheckFields($page = 1) {
        $page = (int) $page;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->limit = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->order = "COALESCE(l10n.title, t.title) ASC";
        $criteria->together = true;

        $fields = GlobalCheckField::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            ),
        ))->findAll($criteria);

        $fieldCount = CheckCategory::model()->count($criteria);
        $paginator = new Paginator($fieldCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Customization"), $this->createUrl("customization/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Checks"), $this->createUrl("customization/checks"));
        $this->breadcrumbs[] = array(Yii::t("app", "Fields"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Fields");
        $this->render("check/field/index", array(
            "fields" => $fields,
            "p" => $paginator,
        ));
    }

    /**
     * Edit field
     * @param $id
     */
    public function actionEditCheckField($id = 0) {
        $id = (int) $id;
        $newRecord = false;

        if ($id) {
            $field = GlobalCheckField::model()->findByPk($id);
        } else {
            $field = new GlobalCheckField();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

        $form = new GlobalCheckFieldEditForm();
        $form->localizedItems = array();

        if (!$newRecord) {
            $form->name = $field->name;
            $form->type = $field->type;
            $form->hidden = $field->hidden;

            $l10n = GlobalCheckFieldL10n::model()->findAllByAttributes(array(
                "global_check_field_id" => $field->id
            ));

            foreach ($l10n as $l) {
                $i = [];
                $i["title"] = $l->title;

                $form->localizedItems[$l->language_id] = $i;
            }
        }

        // collect user input data
        if (isset($_POST["GlobalCheckFieldEditForm"])) {
            $form->attributes = $_POST["GlobalCheckFieldEditForm"];

            if ($form->validate()) {
                $field->title = $form->defaultL10n($languages, "title");
                $field->name = $form->name;
                $field->type = $form->type;
                $field->hidden = isset($_POST["GlobalCheckFieldEditForm"]["hidden"]);
                $field->save();

                $title = null;

                foreach ($form->localizedItems as $languageId => $value) {
                    $fieldL10n = GlobalCheckFieldL10n::model()->findByAttributes(array(
                        "global_check_field_id" => $field->id,
                        "language_id" => $languageId
                    ));

                    if (!$fieldL10n) {
                        $fieldL10n = new GlobalCheckFieldL10n();
                        $fieldL10n->global_check_field_id = $field->id;
                        $fieldL10n->language_id = $languageId;
                    }

                    $fieldL10n->title = $value["title"];
                    $fieldL10n->save();
                }

                // add new field to checks
                if ($newRecord) {
                    TargetCheckReindexJob::enqueue([
                        "global_check_field_id" => $field->id
                    ]);
                }

                Yii::app()->user->setFlash("success", Yii::t("app", "Field saved."));

                if ($newRecord) {
                    $this->redirect(array("customization/editcheckfield", "id" => $field->id));
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Customization"), $this->createUrl("customization/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Checks"), $this->createUrl("customization/checks"));
        $this->breadcrumbs[] = array(Yii::t("app", "Fields"), $this->createUrl("customization/checks/fields"));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t("app", "New Field"), "");
        } else {
            $this->breadcrumbs[] = array(Yii::t("app", $field->localizedTitle), "");
        }

        // display the page
        $this->pageTitle = $newRecord ? Yii::t("app", "New Field") : $field->localizedTitle;
        $this->render("check/field/edit", array(
            "newRecord" => $newRecord,
            "form" => $form,
            "field" => $field,
            "languages" => $languages
        ));
    }


    /**
     * Field control function.
     */
    public function actionControlCheckField() {
        $response = new AjaxResponse();

        try {
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

            $field = GlobalCheckField::model()->findByPk($form->id);

            if (!$field) {
                throw new Exception("Field not found", 404);
            }

            switch ($form->operation) {
                case "delete":
                    if (in_array($field->name, GlobalCheckField::$system)) {
                        throw new Exception("Access denied.", 403);
                    }

                    GlobalCheckField::model()->deleteByPk($form->id);

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}