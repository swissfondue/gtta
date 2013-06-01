<?php

/**
 * Guided test controller.
 */
class GtController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
            'https',
			'checkAuth',
            'checkAdmin',
            'ajaxOnly + control, controltype, controlmodule, controlcheck',
            'postOnly + control, controltype, controlmodule, controlcheck',
		);
	}

    /**
     * Display a list of GT categories.
     */
	public function actionIndex($page=1)
	{
        $page = (int) $page;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 'COALESCE(l10n.name, t.name) ASC';
        $criteria->together = true;

        $categories = GtCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            ),
        ))->findAll($criteria);

        $categoryCount = GtCategory::model()->count($criteria);
        $paginator = new Paginator($categoryCount, $page);
        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Guided Test Templates');
		$this->render('index', array(
            'categories' => $categories,
            'p' => $paginator,
        ));
	}

    /**
     * Display a list of GT types.
     */
	public function actionView($id, $page=1)
	{
        $id = (int) $id;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = GtCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order = 'COALESCE(l10n.name, t.name) ASC';
        $criteria->addColumnCondition(array('gt_category_id' => $category->id));
        $criteria->together = true;

        $types = GtType::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findAll($criteria);

        $typeCount = GtType::model()->count($criteria);
        $paginator = new Paginator($typeCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), $this->createUrl('gt/index'));
        $this->breadcrumbs[] = array($category->localizedName, '');

        // display the page
        $this->pageTitle = $category->localizedName;
		$this->render('view', array(
            'types' => $types,
            'p' => $paginator,
            'category' => $category,
        ));
	}

    /**
     * GT category edit page.
     */
	public function actionEdit($id=0)
	{
        $id = (int) $id;
        $newRecord = false;

        if ($id) {
            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if ($language) {
                $language = $language->id;
            }

            $category = GtCategory::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array('language_id' => $language)
                )
            ))->findByPk($id);
        } else {
            $category = new GtCategory();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new GtCategoryEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->name = $category->name;

            $categoryL10n = GtCategoryL10n::model()->findAllByAttributes(array(
                'gt_category_id' => $category->id
            ));

            foreach ($categoryL10n as $cl) {
                $model->localizedItems[$cl->language_id]['name'] = $cl->name;
            }
        }

		// collect user input data
		if (isset($_POST['GtCategoryEditForm'])) {
			$model->attributes = $_POST['GtCategoryEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

			if ($model->validate()) {
                $category->name = $model->name;
                $category->save();

                foreach ($model->localizedItems as $languageId => $value) {
                    $categoryL10n = GtCategoryL10n::model()->findByAttributes(array(
                        'gt_category_id' => $category->id,
                        'language_id' => $languageId
                    ));

                    if (!$categoryL10n) {
                        $categoryL10n = new GtCategoryL10n();
                        $categoryL10n->gt_category_id = $category->id;
                        $categoryL10n->language_id = $languageId;
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
                    $this->redirect(array('gt/edit', 'id' => $category->id));
                }
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), $this->createUrl('gt/index'));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t('app', 'New Category'), '');
        } else {
            $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('gt/view', array('id' => $category->id)));
            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Category') : $category->localizedName;
		$this->render('edit', array(
            'model' => $model,
            'category' => $category,
            'languages' => $languages,
        ));
	}

    /**
     * GT category control function.
     */
    public function actionControl()
    {
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
            $category = GtCategory::model()->findByPk($id);

            if ($category === null) {
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));
            }

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
     * Type edit page.
     */
	public function actionEditType($id, $type=0)
	{
        $id = (int) $id;
        $type = (int) $type;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = GtCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));
        }

        if ($type) {
            $type = GtType::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array('language_id' => $language)
                )
            ))->findByAttributes(array(
                'id' => $type,
                'gt_category_id' => $category->id
            ));

            if (!$type) {
                throw new CHttpException(404, Yii::t('app', 'Type not found.'));
            }
        } else {
            $type = new GtType();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new GtTypeEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->name = $type->name;

            $typeL10n = GtTypeL10n::model()->findAllByAttributes(array(
                'gt_type_id' => $type->id
            ));

            foreach ($typeL10n as $cl) {
                $i = array();
                $i['name'] = $cl->name;
                $model->localizedItems[$cl->language_id] = $i;
            }
        }

		// collect user input data
		if (isset($_POST['GtTypeEditForm'])) {
			$model->attributes = $_POST['GtTypeEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

			if ($model->validate()) {
                $type->gt_category_id = $category->id;
                $type->name = $model->name;
                $type->save();

                foreach ($model->localizedItems as $languageId => $value) {
                    $typeL10n = GtTypeL10n::model()->findByAttributes(array(
                        'gt_type_id' => $type->id,
                        'language_id' => $languageId
                    ));

                    if (!$typeL10n) {
                        $typeL10n = new GtTypeL10n();
                        $typeL10n->gt_type_id = $type->id;
                        $typeL10n->language_id = $languageId;
                    }

                    if ($value['name'] == '') {
                        $value['name'] = null;
                    }

                    $typeL10n->name = $value['name'];
                    $typeL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Type saved.'));
                $type->refresh();

                if ($newRecord) {
                    $this->redirect(array('gt/edittype', 'id' => $type->gt_category_id, 'type' => $type->id));
                }
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), $this->createUrl('gt/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('gt/view', array('id' => $category->id)));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t('app', 'New Type'), '');
        } else {
            $this->breadcrumbs[] = array($type->localizedName, $this->createUrl('gt/viewtype', array(
                'id' => $category->id,
                'type' => $type->id
            )));

            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Type') : $type->localizedName;
		$this->render('type/edit', array(
            'model' => $model,
            'category' => $category,
            'type' => $type,
            'languages' => $languages,
        ));
	}

    /**
     * Type control function.
     */
    public function actionControlType()
    {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error){
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id = $model->id;
            $type = GtType::model()->findByPk($id);

            if ($type === null) {
                throw new CHttpException(404, Yii::t('app', 'Type not found.'));
            }

            switch ($model->operation) {
                case 'delete':
                    $type->delete();
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
     * Display a list of modules.
     */
	public function actionViewType($id, $type, $page=1)
	{
        $id = (int) $id;
        $type = (int) $type;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = GtCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));
        }

        $type = GtType::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByAttributes(array(
            'id' => $type,
            'gt_category_id' => $category->id
        ));

        if (!$type) {
            throw new CHttpException(404, Yii::t('app', 'Type not found.'));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 'COALESCE(l10n.name, t.name) ASC';
        $criteria->addColumnCondition(array('gt_type_id' => $type->id));
        $criteria->together = true;

        $modules = GtModule::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findAll($criteria);

        $moduleCount = GtModule::model()->count($criteria);
        $paginator = new Paginator($moduleCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), $this->createUrl('gt/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('gt/view', array('id' => $category->id)));
        $this->breadcrumbs[] = array($type->localizedName, '');

        // display the page
        $this->pageTitle = $type->localizedName;
		$this->render('type/index', array(
            'modules' => $modules,
            'p' => $paginator,
            'category' => $category,
            'type' => $type,
        ));
	}

    /**
     * Module edit page.
     */
	public function actionEditModule($id, $type, $module=0)
	{
        $id = (int) $id;
        $type = (int) $type;
        $module = (int) $module;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = GtCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));
        }

        $type = GtType::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByAttributes(array(
            'id' => $type,
            'gt_category_id' => $category->id
        ));

        if (!$type) {
            throw new CHttpException(404, Yii::t('app', 'Type not found.'));
        }

        if ($module) {
            $module = GtModule::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array('language_id' => $language)
                )
            ))->findByAttributes(array(
                'id' => $module,
                'gt_type_id' => $type->id
            ));

            if (!$module) {
                throw new CHttpException(404, Yii::t('app', 'Module not found.'));
            }
        } else {
            $module = new GtModule();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new GtModuleEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->name = $module->name;

            $moduleL10n = GtModuleL10n::model()->findAllByAttributes(array(
                'gt_module_id' => $module->id
            ));

            foreach ($moduleL10n as $cl) {
                $i = array();
                $i['name']           = $cl->name;
                $model->localizedItems[$cl->language_id] = $i;
            }
        }

		// collect user input data
		if (isset($_POST['GtModuleEditForm'])) {
			$model->attributes = $_POST['GtModuleEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

			if ($model->validate()) {
                $module->gt_type_id = $type->id;
                $module->name = $model->name;
                $module->save();

                foreach ($model->localizedItems as $languageId => $value) {
                    $moduleL10n = GtModuleL10n::model()->findByAttributes(array(
                        'gt_module_id' => $module->id,
                        'language_id' => $languageId
                    ));

                    if (!$moduleL10n) {
                        $moduleL10n = new GtModuleL10n();
                        $moduleL10n->gt_module_id = $module->id;
                        $moduleL10n->language_id = $languageId;
                    }

                    if ($value['name'] == '') {
                        $value['name'] = null;
                    }

                    $moduleL10n->name = $value['name'];
                    $moduleL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Module saved.'));

                $module->refresh();

                if ($newRecord) {
                    $this->redirect(array('gt/editmodule', 'id' => $category->id, 'type' => $type->id, 'module' => $module->id));
                }
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), $this->createUrl('gt/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('gt/view', array('id' => $category->id)));
        $this->breadcrumbs[] = array($type->localizedName, $this->createUrl('gt/viewtype', array('id' => $category->id, 'type' => $type->id)));
        $this->breadcrumbs[] = $newRecord ? array(Yii::t('app', 'New Module'), '') : array($module->localizedName, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Module') : $module->localizedName;
		$this->render('type/module/edit', array(
            'model' => $model,
            'category' => $category,
            'type' => $type,
            'module' => $module,
            'languages' => $languages,
        ));
	}

    /**
     * Module control function.
     */
    public function actionControlModule()
    {
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
            $module = GtModule::model()->findByPk($id);

            if ($module === null) {
                throw new CHttpException(404, Yii::t('app', 'Module not found.'));
            }

            switch ($model->operation) {
                case 'delete':
                    $module->delete();
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
     * Display a list of module checks.
     */
	public function actionViewModule($id, $type, $module, $page=1)
	{
        $id = (int) $id;
        $type = (int) $type;
        $module = (int) $module;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = GtCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));
        }

        $type = GtType::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByAttributes(array(
            'id' => $type,
            'gt_category_id' => $category->id
        ));

        if (!$type) {
            throw new CHttpException(404, Yii::t('app', 'Type not found.'));
        }

        $module = GtModule::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByAttributes(array(
            'id' => $module,
            'gt_type_id' => $type->id
        ));

        if (!$module) {
            throw new CHttpException(404, Yii::t('app', 'Module not found.'));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));
        }

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.sort_order ASC';
        $criteria->addColumnCondition(array('gt_module_id' => $module->id));
        $criteria->together = true;

        $checks = GtCheck::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            ),
            'check' => array(
                'with' => array(
                    'l10n' => array(
                        'alias'    => 'l10n_c',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n_c.language_id = :language_id',
                        'params'   => array('language_id' => $language)
                    ),
                )
            )
        ))->findAll($criteria);

        $checkCount = GtCheck::model()->count($criteria);
        $paginator = new Paginator($checkCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), $this->createUrl('gt/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('gt/view', array('id' => $category->id)));
        $this->breadcrumbs[] = array($type->localizedName, $this->createUrl('gt/viewtype', array('id' => $category->id, 'type' => $type->id)));
        $this->breadcrumbs[] = array($module->localizedName, '');

        // display the page
        $this->pageTitle = $module->localizedName;
		$this->render('type/module/index', array(
            'checks' => $checks,
            'p' => $paginator,
            'category' => $category,
            'type' => $type,
            'module' => $module,
        ));
	}

    /**
     * Module check edit page.
     */
	public function actionEditCheck($id, $type, $module, $check=0)
	{
        $id = (int) $id;
        $type = (int) $type;
        $module = (int) $module;
        $check = (int) $check;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $category = GtCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));
        }

        $type = GtType::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByAttributes(array(
            'id' => $type,
            'gt_category_id' => $category->id
        ));

        if (!$type) {
            throw new CHttpException(404, Yii::t('app', 'Type not found.'));
        }

        $module = GtModule::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findByAttributes(array(
            'id' => $module,
            'gt_type_id' => $type->id
        ));

        if (!$module) {
            throw new CHttpException(404, Yii::t('app', 'Module not found.'));
        }

        if ($check) {
            $check = GtCheck::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array('language_id' => $language)
                )
            ))->findByAttributes(array(
                'id' => $check,
                 'gt_module_id' => $module->id
            ));

            if (!$check) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }
        } else {
            $check = new GtCheck();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new GtCheckEditForm();
        $model->localizedItems = array();
        $controlId = null;

        if (!$newRecord) {
            $model->description = $check->description;
            $model->targetDescription = $check->target_description;
            $model->sortOrder = $check->sort_order;
            $model->checkId = $check->check_id;

            $checkL10n = GtCheckL10n::model()->findAllByAttributes(array(
                'gt_check_id' => $check->id
            ));

            foreach ($checkL10n as $cl) {
                $i = array();
                $i['description']  = $cl->description;
                $i['targetDescription']  = $cl->target_description;
                $model->localizedItems[$cl->language_id] = $i;
            }
        } else {
            // increment last sort_order, if any
            $criteria = new CDbCriteria();
            $criteria->select = 'MAX(sort_order) as max_sort_order';
            $criteria->addColumnCondition(array('gt_module_id' => $module->id));

            $maxOrder = GtCheck::model()->find($criteria);

            if ($maxOrder && $maxOrder->max_sort_order !== null) {
                $model->sortOrder = $maxOrder->max_sort_order + 1;
            }
        }

		// collect user input data
		if (isset($_POST['GtCheckEditForm'])) {
			$model->attributes = $_POST['GtCheckEditForm'];
            $model->description = $model->defaultL10n($languages, 'description');
            $model->targetDescription = $model->defaultL10n($languages, 'target_description');

			if ($model->validate()) {
                $testCheck = GtCheck::model()->findByAttributes(array(
                    'gt_module_id' => $module->id,
                    'check_id' => $model->checkId
                ));

                if (!$testCheck || $testCheck->id == $check->id) {
                    $check->gt_module_id = $module->id;
                    $check->check_id = $model->checkId;
                    $check->sort_order = $model->sortOrder;
                    $check->description = $model->description;
                    $check->target_description = $model->targetDescription;
                    $check->save();

                    foreach ($model->localizedItems as $languageId => $value) {
                        $checkL10n = GtCheckL10n::model()->findByAttributes(array(
                            'gt_check_id' => $check->id,
                            'language_id' => $languageId
                        ));

                        if (!$checkL10n) {
                            $checkL10n = new GtCheckL10n();
                            $checkL10n->gt_check_id = $check->id;
                            $checkL10n->language_id = $languageId;
                        }

                        if ($value['description'] == '') {
                            $value['description'] = null;
                        }

                        if ($value['targetDescription'] == '') {
                            $value['targetDescription'] = null;
                        }

                        $checkL10n->description = $value['description'];
                        $checkL10n->target_description = $value['targetDescription'];
                        $checkL10n->save();
                    }

                    Yii::app()->user->setFlash('success', Yii::t('app', 'Check saved.'));
                    $module->refresh();

                    if ($newRecord) {
                        $this->redirect(array('gt/editcheck', 'id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id));
                    }
                } else {
                    $model->addError('checkId', Yii::t('app', 'Check already exists in this module.'));
                }
            }

            if (count($model->getErrors()) > 0) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        $checkName = null;
        $checks = array();

        if (!$newRecord) {
            $checkData = Check::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array('language_id' => $language)
                )
            ))->findByAttributes(array(
                'id' => $check->check_id,
            ));

            $checkName = $checkData->localizedName;
            $controlId = $checkData->check_control_id;

            $checks = Check::model()->with(array(
                'l10n' => array(
                    'alias'    => 'l10n_c',
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'l10n_c.language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findAllByAttributes(array(
                'check_control_id' => $controlId
            ));
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Guided Test Templates'), $this->createUrl('gt/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('gt/view', array('id' => $category->id)));
        $this->breadcrumbs[] = array($type->localizedName, $this->createUrl('gt/viewtype', array('id' => $category->id, 'type' => $type->id)));
        $this->breadcrumbs[] = array($module->localizedName, $this->createUrl('gt/viewmodule', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id)));
        $this->breadcrumbs[] = $newRecord ? array(Yii::t('app', 'New Check'), '') : array($checkName, '');

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),

            'controls' => array(
                'joinType' => 'LEFT JOIN',
                'with'     => array(
                    'l10n' => array(
                        'alias'    => 'l10n_c',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n_c.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                )
            )
        ))->findAllByAttributes(
            array(),
            array( 'order' => 'COALESCE(l10n.name, t.name) ASC' )
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Check') : $checkName;
		$this->render('type/module/check/edit', array(
            'model' => $model,
            'category' => $category,
            'type' => $type,
            'check' => $check,
            'module' => $module,
            'languages' => $languages,
            'categories' => $categories,
            'checks' => $checks,
            'controlId' => $controlId
        ));
	}

    /**
     * Check control function.
     */
    public function actionControlCheck()
    {
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
            $check = GtCheck::model()->findByPk($id);

            if ($check === null) {
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
            }

            switch ($model->operation) {
                case 'delete':
                    $check->delete();
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
