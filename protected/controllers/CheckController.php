<?php

/**
 * Check controller.
 */
class CheckController extends Controller
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
		);
	}

    /**
     * Display a list of check categories.
     */
	public function actionIndex($page=1)
	{
        $page = (int) $page;

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.name ASC';

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),
            'controls' => array(
                'with' => 'checkCount'
            )
        ))->findAll($criteria);

        $categoryCount = CheckCategory::model()->count($criteria);
        $paginator     = new Paginator($categoryCount, $page);

        $count = Check::model()->count();

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Checks');
		$this->render('index', array(
            'categories' => $categories,
            'p'          => $paginator,
            'count'      => $count
        ));
	}

    /**
     * Display a list of check controls.
     */
	public function actionView($id, $page=1)
	{
        $id   = (int) $id;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.name ASC';
        $criteria->addColumnCondition(array( 'check_category_id' => $category->id ));

        $controls = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),
            'checkCount'
        ))->findAll($criteria);

        $controlCount = CheckControl::model()->count($criteria);
        $paginator    = new Paginator($controlCount, $page);

        $controlIds = array();

        foreach ($controls as $control)
            $controlIds[] = $control->id;

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, '');

        // display the page
        $this->pageTitle = $category->localizedName;
		$this->render('view', array(
            'controls' => $controls,
            'p'        => $paginator,
            'category' => $category,
        ));
	}

    /**
     * Check category edit page.
     */
	public function actionEdit($id=0)
	{
        $id        = (int) $id;
        $newRecord = false;

        if ($id)
        {
            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if ($language)
                $language = $language->id;

            $category = CheckCategory::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByPk($id);
        }
        else
        {
            $category  = new CheckCategory();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new CheckCategoryEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name = $category->name;

            $categoryL10n = CheckCategoryL10n::model()->findAllByAttributes(array(
                'check_category_id' => $category->id
            ));

            foreach ($categoryL10n as $cl)
                $model->localizedItems[$cl->language_id]['name'] = $cl->name;
        }

		// collect user input data
		if (isset($_POST['CheckCategoryEditForm']))
		{
			$model->attributes = $_POST['CheckCategoryEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

			if ($model->validate())
            {
                $category->name = $model->name;
                $category->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $categoryL10n = CheckCategoryL10n::model()->findByAttributes(array(
                        'check_category_id' => $category->id,
                        'language_id'       => $languageId
                    ));

                    if (!$categoryL10n)
                    {
                        $categoryL10n = new CheckCategoryL10n();
                        $categoryL10n->check_category_id = $category->id;
                        $categoryL10n->language_id       = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = NULL;

                    $categoryL10n->name = $value['name'];
                    $categoryL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Category saved.'));

                $category->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/edit', 'id' => $category->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Category'), '');
        else
        {
            $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Category') : $category->localizedName;
		$this->render('edit', array(
            'model'     => $model,
            'category'  => $category,
            'languages' => $languages,
        ));
	}

    /**
     * Check category control function.
     */
    public function actionControl()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id       = $model->id;
            $category = CheckCategory::model()->findByPk($id);

            if ($category === null)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $category->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Check control edit page.
     */
	public function actionEditControl($id, $control=0)
	{
        $id        = (int) $id;
        $control   = (int) $control;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        if ($control)
        {
            $control = CheckControl::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id'                => $control,
                'check_category_id' => $category->id
            ));

            if (!$control)
                throw new CHttpException(404, Yii::t('app', 'Control not found.'));
        }
        else
        {
            $control   = new CheckControl();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new CheckControlEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name       = $control->name;
            $model->categoryId = $control->check_category_id;

            $controlL10n = CheckControlL10n::model()->findAllByAttributes(array(
                'check_control_id' => $control->id
            ));

            foreach ($controlL10n as $cl)
            {
                $i = array();

                $i['name'] = $cl->name;
                $model->localizedItems[$cl->language_id] = $i;
            }
        }
        else
            $model->categoryId = $category->id;

		// collect user input data
		if (isset($_POST['CheckControlEditForm']))
		{
			$model->attributes = $_POST['CheckControlEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

			if ($model->validate())
            {
                $redirect = false;

                if ($model->categoryId != $control->check_category_id || $newRecord)
                    $redirect = true;

                $control->check_category_id = $model->categoryId;
                $control->name              = $model->name;
                $control->save();

                if (!$newRecord)
                    TargetCheckCategory::updateAllStats();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $controlL10n = CheckControlL10n::model()->findByAttributes(array(
                        'check_control_id' => $control->id,
                        'language_id'      => $languageId
                    ));

                    if (!$controlL10n)
                    {
                        $controlL10n = new CheckControlL10n();
                        $controlL10n->check_control_id = $control->id;
                        $controlL10n->language_id      = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = NULL;

                    $controlL10n->name = $value['name'];
                    $controlL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Control saved.'));

                $control->refresh();

                if ($redirect)
                    $this->redirect(array( 'check/editcontrol', 'id' => $control->check_category_id, 'control' => $control->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Control'), '');
        else
        {
            $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array(
                'id'      => $category->id,
                'control' => $control->id
            )));
            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Control') : $control->localizedName;
		$this->render('control/edit', array(
            'model'      => $model,
            'category'   => $category,
            'control'    => $control,
            'languages'  => $languages,
            'categories' => $categories
        ));
	}

    /**
     * Check control control function.
     */
    public function actionControlControl()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id      = $model->id;
            $control = CheckControl::model()->findByPk($id);

            if ($control === null)
                throw new CHttpException(404, Yii::t('app', 'Control not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $control->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Display a list of checks.
     */
	public function actionViewControl($id, $control, $page=1)
	{
        $id      = (int) $id;
        $control = (int) $control;
        $page    = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.name ASC';
        $criteria->addColumnCondition(array( 'check_control_id' => $control->id ));

        $checks = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $checkCount = Check::model()->count($criteria);
        $paginator  = new Paginator($checkCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, '');

        // display the page
        $this->pageTitle = $control->localizedName;
		$this->render('control/index', array(
            'checks'   => $checks,
            'p'        => $paginator,
            'category' => $category,
            'control'  => $control,
        ));
	}

    /**
     * Check edit page.
     */
	public function actionEditCheck($id, $control, $check=0)
	{
        $id        = (int) $id;
        $control   = (int) $control;
        $check     = (int) $check;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        if ($check)
        {
            $check = Check::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id'               => $check,
                'check_control_id' => $control->id
            ));

            if (!$check)
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
        }
        else
        {
            $check     = new Check();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new CheckEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name              = $check->name;
            $model->backgroundInfo    = $check->background_info;
            $model->hints             = $check->hints;
            $model->question          = $check->question;
            $model->script            = $check->script;
            $model->advanced          = $check->advanced;
            $model->automated         = $check->automated;
            $model->protocol          = $check->protocol;
            $model->port              = $check->port;
            $model->multipleSolutions = $check->multiple_solutions;
            $model->referenceId       = $check->reference_id;
            $model->referenceCode     = $check->reference_code;
            $model->referenceUrl      = $check->reference_url;
            $model->effort            = $check->effort;
            $model->controlId         = $check->check_control_id;

            $checkL10n = CheckL10n::model()->findAllByAttributes(array(
                'check_id' => $check->id
            ));

            foreach ($checkL10n as $cl)
            {
                $i = array();

                $i['name']           = $cl->name;
                $i['backgroundInfo'] = $cl->background_info;
                $i['hints']          = $cl->hints;
                $i['question']       = $cl->question;

                $model->localizedItems[$cl->language_id] = $i;
            }
        }
        else
            $model->controlId = $control->id;

		// collect user input data
		if (isset($_POST['CheckEditForm']))
		{
			$model->attributes = $_POST['CheckEditForm'];

            $model->name           = $model->defaultL10n($languages, 'name');
            $model->backgroundInfo = $model->defaultL10n($languages, 'backgroundInfo');
            $model->hints          = $model->defaultL10n($languages, 'hints');
            $model->question       = $model->defaultL10n($languages, 'question');

            if (!isset($_POST['CheckEditForm']['advanced']))
                $model->advanced = false;

            if (!isset($_POST['CheckEditForm']['automated']))
                $model->automated = false;

            if (!isset($_POST['CheckEditForm']['multipleSolutions']))
                $model->multipleSolutions = false;

			if ($model->validate())
            {
                $redirect = false;

                if ($model->controlId != $check->check_control_id || $newRecord)
                    $redirect = true;

                $check->name               = $model->name;
                $check->background_info    = $model->backgroundInfo;
                $check->hints              = $model->hints;
                $check->question           = $model->question;
                $check->script             = $model->script;
                $check->advanced           = $model->advanced;
                $check->automated          = $model->automated;
                $check->multiple_solutions = $model->multipleSolutions;
                $check->protocol           = $model->protocol;
                $check->port               = $model->port;
                $check->check_control_id   = $model->controlId;
                $check->reference_id       = $model->referenceId;
                $check->reference_code     = $model->referenceCode;
                $check->reference_url      = $model->referenceUrl;
                $check->effort             = $model->effort;

                $check->save();

                if (!$newRecord)
                    TargetCheckCategory::updateAllStats();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $checkL10n = CheckL10n::model()->findByAttributes(array(
                        'check_id'    => $check->id,
                        'language_id' => $languageId
                    ));

                    if (!$checkL10n)
                    {
                        $checkL10n = new CheckL10n();
                        $checkL10n->check_id    = $check->id;
                        $checkL10n->language_id = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = NULL;

                    if ($value['backgroundInfo'] == '')
                        $value['backgroundInfo'] = NULL;

                    if ($value['hints'] == '')
                        $value['hints'] = NULL;

                    if ($value['question'] == '')
                        $value['question'] = NULL;

                    $checkL10n->name            = $value['name'];
                    $checkL10n->background_info = $value['backgroundInfo'];
                    $checkL10n->hints           = $value['hints'];
                    $checkL10n->question        = $value['question'];
                    $checkL10n->save();
                }

                $targetCheckCategories = TargetCheckCategory::model()->findAllByAttributes(array(
                    'check_category_id' => $category->id
                ));

                foreach ($targetCheckCategories as $targetCheckCategory)
                    $targetCheckCategory->updateStats();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Check saved.'));

                $check->refresh();

                if ($redirect)
                    $this->redirect(array( 'check/editcheck', 'id' => $check->control->check_category_id, 'control' => $check->check_control_id, 'check' => $check->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Check'), '');
        else
            $this->breadcrumbs[] = array($check->localizedName, '');

        $references = Reference::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

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
            array( 'order' => 't.name ASC' )
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Check') : $check->localizedName;
		$this->render('control/check/edit', array(
            'model'      => $model,
            'category'   => $category,
            'control'    => $control,
            'check'      => $check,
            'languages'  => $languages,
            'references' => $references,
            'categories' => $categories,
            'efforts'    => array( 2, 5, 20, 40, 60, 120 ),
        ));
	}

    /**
     * Check control function.
     */
    public function actionControlCheck()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id    = $model->id;
            $check = Check::model()->findByPk($id);

            if ($check === null)
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $check->delete();
                    TargetCheckCategory::updateAllStats();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Display a list of predefined check results.
     */
	public function actionResults($id, $control, $check, $page=1)
	{
        $id      = (int) $id;
        $control = (int) $control;
        $check   = (int) $check;
        $page    = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'               => $check,
            'check_control_id' => $control->id
        ));

        if (!$check)
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.sort_order ASC';
        $criteria->addColumnCondition(array( 'check_id' => $check->id ));

        $check_results = CheckResult::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $resultCount = CheckResult::model()->count($criteria);
        $paginator   = new Paginator($resultCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )));
        $this->breadcrumbs[] = array($check->localizedName, $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Results'), '');

        // display the page
        $this->pageTitle = $check->localizedName;
		$this->render('control/check/result/index', array(
            'results'  => $check_results,
            'p'        => $paginator,
            'check'    => $check,
            'category' => $category,
            'control'  => $control,
        ));
	}

    /**
     * Check result edit page.
     */
	public function actionEditResult($id, $control, $check, $result=0)
	{
        $id        = (int) $id;
        $control   = (int) $control;
        $check     = (int) $check;
        $result    = (int) $result;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'               => $check,
            'check_control_id' => $control->id
        ));

        if (!$check)
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));

        if ($result)
        {
            $result = CheckResult::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id'       => $result,
                'check_id' => $check->id
            ));

            if (!$result)
                throw new CHttpException(404, Yii::t('app', 'Result not found.'));
        }
        else
        {
            $result    = new CheckResult();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new CheckResultEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->title     = $result->title;
            $model->result    = $result->result;
            $model->sortOrder = $result->sort_order;

            $checkResultL10n = CheckResultL10n::model()->findAllByAttributes(array(
                'check_result_id' => $result->id
            ));

            foreach ($checkResultL10n as $crl)
            {
                $model->localizedItems[$crl->language_id]['title']  = $crl->title;
                $model->localizedItems[$crl->language_id]['result'] = $crl->result;
            }
        }
        else
        {
            // increment last sort_order, if any
            $criteria = new CDbCriteria();
            $criteria->select = 'MAX(sort_order) as max_sort_order';
            $criteria->addColumnCondition(array( 'check_id' => $check->id ));

            $maxOrder = CheckResult::model()->find($criteria);

            if ($maxOrder && $maxOrder->max_sort_order !== NULL)
                $model->sortOrder = $maxOrder->max_sort_order + 1;
        }

		// collect user input data
		if (isset($_POST['CheckResultEditForm']))
		{
			$model->attributes = $_POST['CheckResultEditForm'];
            $model->title  = $model->defaultL10n($languages, 'title');
            $model->result = $model->defaultL10n($languages, 'result');

			if ($model->validate())
            {
                $result->check_id   = $check->id;
                $result->title      = $model->title;
                $result->result     = $model->result;
                $result->sort_order = $model->sortOrder;

                $result->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $checkResultL10n = CheckResultL10n::model()->findByAttributes(array(
                        'check_result_id' => $result->id,
                        'language_id'     => $languageId
                    ));

                    if (!$checkResultL10n)
                    {
                        $checkResultL10n = new CheckResultL10n();
                        $checkResultL10n->check_result_id = $result->id;
                        $checkResultL10n->language_id     = $languageId;
                    }

                    if ($value['title'] == '')
                        $value['title'] = NULL;

                    if ($value['result'] == '')
                        $value['result'] = NULL;

                    $checkResultL10n->title  = $value['title'];
                    $checkResultL10n->result = $value['result'];
                    $checkResultL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Result saved.'));

                $result->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/editresult', 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'result' => $result->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )));
        $this->breadcrumbs[] = array($check->localizedName, $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Results'), $this->createUrl('check/results', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Result'), '');
        else
            $this->breadcrumbs[] = array($result->localizedTitle, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Result') : $result->localizedTitle;
		$this->render('control/check/result/edit', array(
            'model'     => $model,
            'category'  => $category,
            'control'   => $control,
            'check'     => $check,
            'result'    => $result,
            'languages' => $languages,
        ));
	}

    /**
     * Result control function.
     */
    public function actionControlResult()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id     = $model->id;
            $result = CheckResult::model()->findByPk($id);

            if ($result === null)
                throw new CHttpException(404, Yii::t('app', 'Result not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $result->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Display a list of check solutions.
     */
	public function actionSolutions($id, $control, $check, $page=1)
	{
        $id      = (int) $id;
        $control = (int) $control;
        $check   = (int) $check;
        $page    = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'               => $check,
            'check_control_id' => $control->id
        ));

        if (!$check)
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.sort_order ASC';
        $criteria->addColumnCondition(array( 'check_id' => $check->id ));

        $check_solutions = CheckSolution::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $solutionCount = CheckSolution::model()->count($criteria);
        $paginator     = new Paginator($solutionCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )));
        $this->breadcrumbs[] = array($check->localizedName, $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Solutions'), '');

        // display the page
        $this->pageTitle = $check->localizedName;
		$this->render('control/check/solution/index', array(
            'solutions' => $check_solutions,
            'p'         => $paginator,
            'check'     => $check,
            'category'  => $category,
            'control'   => $control
        ));
	}

    /**
     * Check solution edit page.
     */
	public function actionEditSolution($id, $control, $check, $solution=0)
	{
        $id        = (int) $id;
        $control   = (int) $control;
        $check     = (int) $check;
        $solution  = (int) $solution;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'               => $check,
            'check_control_id' => $control->id
        ));

        if (!$check)
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));

        if ($solution)
        {
            $solution = CheckSolution::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id'       => $solution,
                'check_id' => $check->id
            ));

            if (!$solution)
                throw new CHttpException(404, Yii::t('app', 'Solution not found.'));
        }
        else
        {
            $solution  = new CheckSolution();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new CheckSolutionEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->title     = $solution->title;
            $model->solution  = $solution->solution;
            $model->sortOrder = $solution->sort_order;

            $checkSolutionL10n = CheckSolutionL10n::model()->findAllByAttributes(array(
                'check_solution_id' => $solution->id
            ));

            foreach ($checkSolutionL10n as $csl)
            {
                $model->localizedItems[$csl->language_id]['title']    = $csl->title;
                $model->localizedItems[$csl->language_id]['solution'] = $csl->solution;
            }
        }
        else
        {
            // increment last sort_order, if any
            $criteria = new CDbCriteria();
            $criteria->select = 'MAX(sort_order) as max_sort_order';
            $criteria->addColumnCondition(array( 'check_id' => $check->id ));

            $maxOrder = CheckSolution::model()->find($criteria);

            if ($maxOrder && $maxOrder->max_sort_order !== NULL)
                $model->sortOrder = $maxOrder->max_sort_order + 1;
        }

		// collect user input data
		if (isset($_POST['CheckSolutionEditForm']))
		{
			$model->attributes = $_POST['CheckSolutionEditForm'];
            $model->title    = $model->defaultL10n($languages, 'title');
            $model->solution = $model->defaultL10n($languages, 'solution');

			if ($model->validate())
            {
                $solution->check_id   = $check->id;
                $solution->title      = $model->title;
                $solution->solution   = $model->solution;
                $solution->sort_order = $model->sortOrder;

                $solution->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $checkSolutionL10n = CheckSolutionL10n::model()->findByAttributes(array(
                        'check_solution_id' => $solution->id,
                        'language_id'       => $languageId
                    ));

                    if (!$checkSolutionL10n)
                    {
                        $checkSolutionL10n = new CheckSolutionL10n();
                        $checkSolutionL10n->check_solution_id = $solution->id;
                        $checkSolutionL10n->language_id       = $languageId;
                    }

                    if ($value['solution'] == '')
                        $value['solution'] = NULL;

                    if ($value['title'] == '')
                        $value['title'] = NULL;

                    $checkSolutionL10n->title    = $value['title'];
                    $checkSolutionL10n->solution = $value['solution'];
                    $checkSolutionL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Solution saved.'));

                $solution->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/editsolution', 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'solution' => $solution->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )));
        $this->breadcrumbs[] = array($check->localizedName, $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Solutions'), $this->createUrl('check/solutions', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Solution'), '');
        else
            $this->breadcrumbs[] = array($solution->localizedTitle, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Solution') : $solution->localizedTitle;
		$this->render('control/check/solution/edit', array(
            'model'     => $model,
            'category'  => $category,
            'control'   => $control,
            'check'     => $check,
            'solution'  => $solution,
            'languages' => $languages,
        ));
	}

    /**
     * Solution control function.
     */
    public function actionControlSolution()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id       = $model->id;
            $solution = CheckSolution::model()->findByPk($id);

            if ($solution === null)
                throw new CHttpException(404, Yii::t('app', 'Solution not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $solution->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Display a list of check inputs.
     */
	public function actionInputs($id, $control, $check, $page=1)
	{
        $id      = (int) $id;
        $control = (int) $control;
        $check   = (int) $check;
        $page    = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'               => $check,
            'check_control_id' => $control->id
        ));

        if (!$check)
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.sort_order ASC';
        $criteria->addColumnCondition(array( 'check_id' => $check->id ));

        $check_inputs = CheckInput::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $inputCount = CheckInput::model()->count($criteria);
        $paginator  = new Paginator($inputCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )));
        $this->breadcrumbs[] = array($check->localizedName, $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Inputs'), '');

        // display the page
        $this->pageTitle = $check->localizedName;
		$this->render('control/check/input/index', array(
            'inputs'   => $check_inputs,
            'p'        => $paginator,
            'check'    => $check,
            'category' => $category,
            'control'  => $control,
        ));
	}

    /**
     * Check input edit page.
     */
	public function actionEditInput($id, $control, $check, $input=0)
	{
        $id        = (int) $id;
        $control   = (int) $control;
        $check     = (int) $check;
        $input     = (int) $input;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $control = CheckControl::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $control,
            'check_category_id' => $category->id
        ));

        if (!$control)
            throw new CHttpException(404, Yii::t('app', 'Control not found.'));

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'               => $check,
            'check_control_id' => $control->id
        ));

        if (!$check)
            throw new CHttpException(404, Yii::t('app', 'Check not found.'));

        if ($input)
        {
            $input = CheckInput::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id'       => $input,
                'check_id' => $check->id
            ));

            if (!$input)
                throw new CHttpException(404, Yii::t('app', 'Input not found.'));
        }
        else
        {
            $input     = new CheckInput();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new CheckInputEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name        = $input->name;
            $model->description = $input->description;
            $model->value       = $input->value;
            $model->sortOrder   = $input->sort_order;

            $checkInputL10n = CheckInputL10n::model()->findAllByAttributes(array(
                'check_input_id' => $input->id
            ));

            foreach ($checkInputL10n as $cil)
            {
                $model->localizedItems[$cil->language_id]['name']        = $cil->name;
                $model->localizedItems[$cil->language_id]['description'] = $cil->description;
                $model->localizedItems[$cil->language_id]['value']       = $cil->value;
            }
        }
        else
        {
            // increment last sort_order, if any
            $criteria = new CDbCriteria();
            $criteria->select = 'MAX(sort_order) as max_sort_order';
            $criteria->addColumnCondition(array( 'check_id' => $check->id ));

            $maxOrder = CheckInput::model()->find($criteria);

            if ($maxOrder && $maxOrder->max_sort_order !== NULL)
                $model->sortOrder = $maxOrder->max_sort_order + 1;
        }

		// collect user input data
		if (isset($_POST['CheckInputEditForm']))
		{
			$model->attributes = $_POST['CheckInputEditForm'];
            $model->name        = $model->defaultL10n($languages, 'name');
            $model->description = $model->defaultL10n($languages, 'description');
            $model->value       = $model->defaultL10n($languages, 'value');

			if ($model->validate())
            {
                $input->check_id    = $check->id;
                $input->name        = $model->name;
                $input->description = $model->description;
                $input->value       = $model->value;
                $input->sort_order  = $model->sortOrder;

                $input->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $checkInputL10n = CheckInputL10n::model()->findByAttributes(array(
                        'check_input_id' => $input->id,
                        'language_id'    => $languageId
                    ));

                    if (!$checkInputL10n)
                    {
                        $checkInputL10n = new CheckInputL10n();
                        $checkInputL10n->check_input_id = $input->id;
                        $checkInputL10n->language_id    = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = NULL;

                    if ($value['description'] == '')
                        $value['description'] = NULL;

                    if ($value['value'] == '')
                        $value['value'] = NULL;

                    $checkInputL10n->name        = $value['name'];
                    $checkInputL10n->description = $value['description'];
                    $checkInputL10n->value       = $value['value'];
                    $checkInputL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Input saved.'));

                $input->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/editinput', 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'input' => $input->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array($category->localizedName, $this->createUrl('check/view', array( 'id' => $category->id )));
        $this->breadcrumbs[] = array($control->localizedName, $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )));
        $this->breadcrumbs[] = array($check->localizedName, $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Inputs'), $this->createUrl('check/inputs', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Input'), '');
        else
            $this->breadcrumbs[] = array($input->localizedName, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Input') : $input->localizedName;
		$this->render('control/check/input/edit', array(
            'model'     => $model,
            'category'  => $category,
            'control'   => $control,
            'check'     => $check,
            'input'     => $input,
            'languages' => $languages,
        ));
	}

    /**
     * Input control function.
     */
    public function actionControlInput()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id    = $model->id;
            $input = CheckInput::model()->findByPk($id);

            if ($input === null)
                throw new CHttpException(404, Yii::t('app', 'Input not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $input->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Search action.
     */
    public function actionSearch()
    {
        $model  = new SearchForm();
        $checks = array();

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        if (isset($_POST['SearchForm']))
        {
            $model->attributes = $_POST['SearchForm'];

            if ($model->validate())
            {
                $criteria = new CDbCriteria();
                $criteria->order = 't.name ASC';
                $criteria->addColumnCondition(array( 'language_id' => $language ));

                $searchCriteria = new CDbCriteria();
                $searchCriteria->addSearchCondition('t.name', $model->query, true, 'OR', 'ILIKE');
                $searchCriteria->addSearchCondition('t.background_info', $model->query, true, 'OR', 'ILIKE');
                $searchCriteria->addSearchCondition('t.hints', $model->query, true, 'OR', 'ILIKE');
                $searchCriteria->addSearchCondition('t.question', $model->query, true, 'OR', 'ILIKE');
                $criteria->mergeWith($searchCriteria);

                $checks = CheckL10n::model()->findAll($criteria);
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Checks'), $this->createUrl('check/index'));
        $this->breadcrumbs[] = array(Yii::t('app', 'Search'), '');

		// display the page
        $this->pageTitle = Yii::t('app', 'Search');
		$this->render('search', array(
            'model'  => $model,
            'checks' => $checks,
        ));
    }
}
