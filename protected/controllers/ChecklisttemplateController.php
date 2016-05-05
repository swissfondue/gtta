<?php

/**
 * Class CheckListTemplateController
 */
class ChecklisttemplateController extends Controller {
    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'https',
            'checkAuth',
            'checkAdmin',
            'ajaxOnly + controlcategory, controltemplate, controlcheckcategory',
            'postOnly + controlcategory, controltemplate, controlcheckcategory',
        );
    }

    /**
     * List of check list template categories
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
        $criteria->together = true;

        $categories = ChecklistTemplateCategory::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array( "language_id" => $language )
            )
        ))->findAll($criteria);
        $categoryCount = ChecklistTemplateCategory::model()->count($criteria);

        $paginator = new Paginator($categoryCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Checklist Templates"), "");
        $this->pageTitle = Yii::t("app", "Checklist Templates");

        $count = ChecklistTemplate::model()->count();
        $this->render("index", array(
            "categories" => $categories,
            "p" => $paginator,
            "count" => $count,
        ));
    }

    /**
     * View category templates
     */
    public function actionViewCategory($id, $page=1) {
        $id = (int) $id;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = ChecklistTemplateCategory::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array( "language_id" => $language )
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t("app", "Category not found."));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->params["entriesPerPage"];
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];
        $criteria->addColumnCondition(array( "checklist_template_category_id" => $category->id ));
        $criteria->together = true;

        $templates = ChecklistTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array( "language_id" => $language )
            ),
        ))->findAll($criteria);

        $templateCount = ChecklistTemplate::model()->count($criteria);
        $paginator = new Paginator($templateCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Checklist Templates"), $this->createUrl("checklisttemplate/index"));
        $this->breadcrumbs[] = array($category->localizedName, "");

        // display the page
        $this->pageTitle = $category->localizedName;
        $this->render("category/view", array(
            "templates" => $templates,
            "p" => $paginator,
            "category" => $category,
        ));
    }

    /**
     * Edit category
     */
    public function actionEditCategory($id=0) {
        $id        = (int) $id;
        $newRecord = false;

        if ($id) {
            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if ($language) {
                $language = $language->id;
            }

            $category = ChecklistTemplateCategory::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByPk($id);
        } else {
            $category  = new ChecklistTemplateCategory();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

        $model = new ChecklistTemplateCategoryEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->name = $category->name;

            $categoryL10n = ChecklistTemplateCategoryL10n::model()->findAllByAttributes(array(
                'checklist_template_category_id' => $category->id
            ));

            foreach ($categoryL10n as $cl) {
                $model->localizedItems[$cl->language_id]['name'] = $cl->name;
            }
        }

        // collect user input data
        if (isset($_POST['ChecklistTemplateCategoryEditForm'])) {
            $model->attributes = $_POST['ChecklistTemplateCategoryEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

            if ($model->validate()) {
                $category->name = $model->name;
                $category->save();

                foreach ($model->localizedItems as $languageId => $value) {
                    $categoryL10n = ChecklistTemplateCategoryL10n::model()->findByAttributes(array(
                        'checklist_template_category_id' => $category->id,
                        'language_id'                    => $languageId
                    ));

                    if (!$categoryL10n) {
                        $categoryL10n = new ChecklistTemplateCategoryL10n();
                        $categoryL10n->checklist_template_category_id = $category->id;
                        $categoryL10n->language_id                    = $languageId;
                    }

                    if ($value['name'] == '') {
                        $value['name'] = null;
                    }

                    $categoryL10n->name = $value['name'];
                    $categoryL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Category saved.'));

                $category->refresh();

                if ($newRecord) {
                    $this->redirect(array( 'checklisttemplate/editcategory', 'id' => $category->id ));
                }

                // refresh the category after saving
                $category = ChecklistTemplateCategory::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findByPk($id);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Checklist Templates'), $this->createUrl('checklisttemplate/index'));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t('app', 'New Category'), '');
        } else {
            $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('checklisttemplate/viewcategory', array( 'id' => $category->id )));
            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

        // display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Category') : $category->localizedName;
        $this->render("category/edit", array(
            "model"     => $model,
            "category"  => $category,
            "languages" => $languages,
        ));
    }

    /**
     * Control category
     */
    public function actionControlCategory() {
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
            $category = ChecklistTemplateCategory::model()->findByPk($id);

            if ($category === null)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            switch ($model->operation) {
                case 'delete':
                    $category->delete();
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

    /**
     * Edit check list template
     */
    public function actionViewTemplate($id, $template, $page=1) {
        $id = (int) $id;
        $page = (int) $page;
        $template = (int) $template;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $lang = array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array( "language_id" => $language )
            )
        );

        $category = ChecklistTemplateCategory::model()->with($lang)->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t("app", "Category not found."));
        }

        $template = ChecklistTemplate::model()->with($lang)->findByPk($template);

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $templateChecks = ChecklistTemplateCheck::model()->findAllByAttributes(array(
            "checklist_template_id" => $template->id
        ));

        $categoryIds = array();

        foreach ($templateChecks as $tc) {
            $categoryIds[] = $tc->check->control->category->id;
        }

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->params["entriesPerPage"];
        $criteria->addInCondition("id", $categoryIds);
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];

        $checkCategories = CheckCategory::model()->findAll($criteria);
        $categoryCount = CheckCategory::model()->count($criteria);
        $paginator = new Paginator($categoryCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Checklist Templates"), $this->createUrl("checklisttemplate/index"));
        $this->breadcrumbs[] = array(Yii::t("app", $category->localizedName), $this->createUrl("checklisttemplate/viewcategory", array( "id" => $category->id )));
        $this->breadcrumbs[] = array($template->localizedName, "");

        // display the page
        $this->pageTitle = $template->localizedName;
        $this->render("category/template/categories", array(
            "checkCategories" => $checkCategories,
            "p" => $paginator,
            "category" => $category,
            "template" => $template,
        ));
    }

    /**
     * Edit template
     */
    public function actionEditTemplate($id, $template=0) {
        $id        = (int) $id;
        $template   = (int) $template;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = ChecklistTemplateCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));
        }

        if ($template) {
            $template = ChecklistTemplate::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                ))
            )->findByAttributes(array(
                        'id'                => $template,
                        'checklist_template_category_id' => $category->id
                ));

            if (!$template) {
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));
            }
        } else {
            $template   = new ChecklistTemplate();
            $template->checklist_template_category_id = $category->id;
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

        $model = new ChecklistTemplateEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->name        = $template->name;
            $model->description = $template->description;

            $templateL10n = ChecklistTemplateL10n::model()->findAllByAttributes(array(
                'checklist_template_id' => $template->id
            ));

            foreach ($templateL10n as $tl) {
                $i = array();

                $i['name'] = $tl->name;
                $i['description'] = $tl->description;
                $model->localizedItems[$tl->language_id] = $i;
            }
        }

        if (isset($_POST['ChecklistTemplateEditForm'])) {
            $model->attributes  = $_POST['ChecklistTemplateEditForm'];
            $model->name        = $model->defaultL10n($languages, 'name');
            $model->description = $model->defaultL10n($languages, 'description');

            if ($model->validate()) {
                $redirect = false;

                if ($newRecord) {
                    $redirect = true;
                }

                $template->name        = $model->name;
                $template->description = $model->description;

                $template->save();

                foreach ($model->localizedItems as $languageId => $value) {
                    $templateL10n = ChecklistTemplateL10n::model()->findByAttributes(array(
                        'checklist_template_id' => $template->id,
                        'language_id' => $languageId
                    ));

                    if (!$templateL10n) {
                        $templateL10n = new ChecklistTemplateL10n();
                        $templateL10n->checklist_template_id = $template->id;
                        $templateL10n->language_id = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = null;

                    if ($value['description'] == '')
                        $value['description'] = null;

                    $templateL10n->name = $value['name'];
                    $templateL10n->description = $value['description'];
                    $templateL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Template saved.'));

                $template->refresh();
                TargetCheckReindexJob::enqueue(array("template_id" => $template->id));

                if ($redirect) {
                    $this->redirect(array('checklisttemplate/edittemplate', 'id' => $category->id, 'template' => $template->id));
                }

                // refresh the control after saving
                $template = ChecklistTemplate::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findByAttributes(array(
                        "id" => $template->id,
                        "checklist_template_category_id" => $category->id
                    ));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Checklist Templates'), $this->createUrl('checklisttemplate/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('checklisttemplate/viewcategory', array( 'id' => $category->id )));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t('app', 'New Template'), '');
        } else {
            $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('checklisttemplate/viewtemplate', array(
                'id'          => $category->id,
                'template'    => $template->id
            )));
            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

        // display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Template') : $template->localizedName;
        $this->render("category/template/edit", array(
            "model" => $model,
            "category" => $category,
            "template" => $template,
            "languages" => $languages,
        ));
    }

    /**
     * Control template
     */
    public function actionControlTemplate() {
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

            $id = $model->id;
            $template = ChecklistTemplate::model()->findByPk($id);

            if ($template === null) {
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));
            }

            if ($model->operation == 'delete') {
                $targetIds = array();

                $targetTemplates = TargetChecklistTemplate::model()->findAllByAttributes(array(
                    "checklist_template_id" => $template->id
                ));

                foreach ($targetTemplates as $tc) {
                    $targetIds[] = $tc->target_id;
                }

                $template->delete();

                foreach ($targetIds as $targetId) {
                    TargetCheckReindexJob::enqueue(array("target_id" => $targetId));
                }
            } else {
                throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Edit template's check category
     * @param $id
     * @param $template
     * @param int $category
     * @throws CHttpException
     */
    public function actionEditCheckCategory($id, $template, $category=0) {
        $id = (int) $id;
        $template = (int) $template;
        $checkCategory = (int) $category;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $lang = array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        );

        $category = ChecklistTemplateCategory::model()->with($lang)->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, "Category not found.");
        }

        $template = ChecklistTemplate::model()->with($lang)->findByAttributes(array(
            "checklist_template_category_id" => $category->id,
            "id" => $template,
        ));

        if (!$template) {
            throw new CHttpException(404, "Template not found.");
        }

        if (!$checkCategory) {
            $newRecord = true;
        } else {
            $checkCategory = CheckCategory::model()->with($lang)->findByPk($checkCategory);
        }

        $model = new ChecklistTemplateCheckCategoryEditForm();
        $checkIds = array();
        $categoryChecks = array();
        
        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if (!$newRecord) {
            $categories = CheckCategory::model()->findAllByPk($checkCategory->id);
            $model->categoryId = $checkCategory->id;
            $criteria = new CDbCriteria();
            $criteria->order = "control.sort_order, t.sort_order";

            $categoryChecks = Check::model()->with(array(
                "control" => array(
                    "with" => array(
                        "category" => array(
                            "alias" => "tcat",
                            "joinType",
                            "condition" => "tcat.id = :category_id",
                            "params" => array(
                                "category_id" => $checkCategory->id
                            )
                        ),
                        "l10n" => array(
                            "alias" => "l10n_c",
                            "on" => "l10n_c.language_id = :language_id",
                            "params" => array("language_id" => $language)
                        )
                    )
                ),
                "l10n" => array(
                    "on" => "l10n.language_id = :language_id",
                    "params" => array("language_id" => $language)
                )
            ))->findAll($criteria);

            foreach ($categoryChecks as $check) {
                $tc = ChecklistTemplateCheck::model()->findByAttributes(array(
                    "checklist_template_id" => $template->id,
                    "check_id" => $check->id
                ));

                if ($tc) {
                    $checkIds[] = $check->id;
                }
            }

            $model->checkIds = $checkIds;
        } else {
            $templateChecks = ChecklistTemplateCheck::model()->findAllByAttributes(array(
                "checklist_template_id" => $template->id
            ));

            $categoryIds = array();

            foreach ($templateChecks as $tc) {
                $categoryIds[] = $tc->check->control->category->id;
            }

            $criteria = new CDbCriteria();
            $criteria->addNotInCondition("id", $categoryIds);

            $categories = CheckCategory::model()->findAll($criteria);
        }

        if (isset($_POST['ChecklistTemplateCheckCategoryEditForm'])) {
            $model->attributes = $_POST['ChecklistTemplateCheckCategoryEditForm'];

            if ($model->validate()) {
                $newChecks = array_values(array_diff($model->checkIds, $checkIds));
                $newChecks = Check::model()->findAllByAttributes(array(
                    "id" => $newChecks,
                ));

                $delChecks = array_values(array_diff($checkIds, $model->checkIds));
                $delChecks = Check::model()->findAllByAttributes(array(
                    "id" => $delChecks
                ));

                foreach ($newChecks as $newCheck) {
                    $tc = new ChecklistTemplateCheck();
                    $tc->checklist_template_id = $template->id;
                    $tc->check_id = $newCheck->id;
                    $tc->save();
                }

                foreach ($delChecks as $delCheck) {
                    $tc = ChecklistTemplateCheck::model()->findByAttributes(array(
                        "checklist_template_id" => $template->id,
                        "check_id" => $delCheck->id
                    ));

                    if ($tc) {
                        $tc->delete();
                    }
                }

                if ($newRecord) {
                    $checkCategory = CheckCategory::model()->with($lang)->findByPk($model->categoryId);
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Category saved.'));
                TargetCheckReindexJob::enqueue(array("template_id" => $template->id));

                $this->redirect(array(
                    'checklisttemplate/editcheckcategory',
                    'id' => $category->id,
                    'template' => $template->id,
                    'category' => $checkCategory->id
                ));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Checklist Templates'), $this->createUrl('checklisttemplate/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('checklisttemplate/viewcategory', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('checklisttemplate/viewtemplate', array( 'id' => $category->id, 'template' => $template->id )));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t('app', 'New Category'), '');
        } else {
            $this->breadcrumbs[] = array($checkCategory->localizedName, "");
        }

        // display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Category') : $checkCategory->localizedName;
        $this->render("category/template/category/edit", array(
            "model"      => $model,
            "categories" => $categories,
            "checks"     => $categoryChecks,
            "newRecord"  => $newRecord,
        ));
    }

    /**
     * Control check category in check list template
     */
    public function actionControlCheckCategory($template) {
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

            $id = $model->id;
            $templateId = (int) $template;

            $category = CheckCategory::model()->findByPk($id);

            if (!$category) {
                throw new CHttpException(404, "Category not found.");
            }

            $template = ChecklistTemplate::model()->findByPk($templateId);

            if ($template === null) {
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));
            }

            if ($model->operation == 'delete') {
                $checkIds = array();

                $categoryChecks = Check::model()->with(array(
                    "control" => array(
                        "with" => array(
                            "category" => array(
                                "alias" => "tcat",
                                "joinType",
                                "condition" => "tcat.id = :category_id",
                                "params" => array(
                                    "category_id" => $category->id
                                )
                            )
                        )
                    )
                ))->findAll();

                foreach ($categoryChecks as $check) {
                    $checkIds[] = $check->id;
                }

                if (!empty($checkIds)) {
                    ChecklistTemplateCheck::model()->deleteAllByAttributes(array(
                        "check_id" => $checkIds,
                        "checklist_template_id" => $template->id,
                    ));
                }
            } else {
                throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
            }

            TargetCheckReindexJob::enqueue(array("template_id" => $template->id));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}