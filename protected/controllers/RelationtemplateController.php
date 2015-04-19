<?php

/**
 * Class RelationtemplateController
 */
class RelationtemplateController extends Controller {
    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            "https",
            "checkAuth",
            "checkAdmin",
            "ajaxOnly + control",
            "postOnly + control"
        );
    }

    /**
     * Relation Templates list page
     * @param int $page
     * @throws CHttpException
     */
    public function actionIndex($page=1) {
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
        $criteria->limit = Yii::app()->params["entriesPerPage"];
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];
        $criteria->order = "COALESCE(l10n.name, t.name) ASC";
        $criteria->together = true;

        $templates = RelationTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            ),
        ))->findAll($criteria);

        $templateCount = RelationTemplate::model()->count($criteria);
        $paginator = new Paginator($templateCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Relation Templates"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Relation Templates");
        $this->render("index", array(
            "templates" => $templates,
            "p" => $paginator,
            "count" => $templateCount,
        ));
    }

    /**
     * Relation Template edit page
     * @param $id
     */
    public function actionEdit($id=0) {
        $id        = (int) $id;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if ($id) {
            $template = RelationTemplate::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array('language_id' => $language)
                )
            ))->findByPk($id);
        } else {
            $template  = new RelationTemplate();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

        $model = new RelationTemplateEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->name = $template->name;

            $templateL10n = RelationTemplateL10n::model()->findAllByAttributes(array(
                'relation_template_id' => $template->id
            ));

            foreach ($templateL10n as $cl) {
                $model->localizedItems[$cl->language_id]['name'] = $cl->name;
            }
        }

        // collect user input data
        if (isset($_POST['RelationTemplateEditForm'])) {
            $model->attributes = $_POST['RelationTemplateEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');
            $success = false;

            if ($model->validate()) {
                try {
                    RelationTemplateManager::validateRelations($model->relations);
                    $success = true;
                } catch (Exception $e) {
                    $model->addError("relations", $e->getMessage());
                }
            }

            if ($success) {
                $template->name = $model->name;
                $template->relations = $model->relations;
                $template->save();

                foreach ($model->localizedItems as $languageId => $value) {
                    $templateL10n = RelationTemplateL10n::model()->findByAttributes(array(
                        'relation_template_id' => $template->id,
                        'language_id'          => $languageId
                    ));

                    if (!$templateL10n) {
                        $templateL10n = new RelationTemplateL10n();
                        $templateL10n->relation_template_id = $template->id;
                        $templateL10n->language_id          = $languageId;
                    }

                    if ($value['name'] == '') {
                        $value['name'] = null;
                    }

                    $templateL10n->name = $value['name'];
                    $templateL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Template saved.'));

                $template->refresh();

                if ($newRecord) {
                    $this->redirect(array('relationtemplate/edit', 'id' => $template->id));
                }

                // refresh the template after saving
                $template = RelationTemplate::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findByPk($id);
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $categories = CheckCategory::model()->findAll();
        $filters = RelationManager::$filters;

        $this->breadcrumbs[] = array(Yii::t('app', 'Relation Templates'), $this->createUrl('relationtemplate/index'));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t('app', 'New Template'), '');
        } else {
            $this->breadcrumbs[] = array($template->localizedName, "");
        }

        // display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Template') : $template->localizedName;
        $this->render("edit", array(
            "model" => $model,
            "template" => $template,
            "languages" => $languages,
            "categories" => $categories,
            "filters" => $filters,
        ));
    }

    /**
     * Relation template control function
     */
    public function actionControl() {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id       = $model->id;
            $template = RelationTemplate::model()->findByPk($id);

            if ($template === null) {
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));
            }

            switch ($model->operation) {
                case 'delete':
                    $template->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}