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
            )
        ))->findAll($criteria);

        $categoryCount = CheckCategory::model()->count($criteria);
        $paginator     = new Paginator($categoryCount, $page);

        $this->breadcrumbs[Yii::t('app', 'Checks')] = '';

        // display the page
        $this->pageTitle = Yii::t('app', 'Checks');
		$this->render('index', array(
            'categories' => $categories,
            'p'          => $paginator
        ));
	}

    /**
     * Display a list of checks.
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

        $checks = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $checkCount = Check::model()->count($criteria);
        $paginator  = new Paginator($checkCount, $page);

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName] = '';

        // display the page
        $this->pageTitle = $category->localizedName;
		$this->render('view', array(
            'checks'   => $checks,
            'p'        => $paginator,
            'category' => $category
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

        $languages       = Language::model()->findAll();
        $defaultLanguage = Language::model()->findByAttributes(
            array( 'default' => true ),
            array( 'order'   => '"default" DESC' )
        );

        if ($defaultLanguage)
            $defaultLanguage = $defaultLanguage->id;

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

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Category')] = '';
        else
        {
            $this->breadcrumbs[$category->localizedName] = $this->createUrl('check/view', array( 'id' => $category->id ));
            $this->breadcrumbs[Yii::t('app', 'Edit')]    = '';
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Category') : $category->localizedName;
		$this->render('edit', array(
            'model'           => $model,
            'category'        => $category,
            'languages'       => $languages,
            'defaultLanguage' => $defaultLanguage
        ));
	}

    /**
     * Control function.
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
     * Check edit page.
     */
	public function actionEditCheck($id, $check=0)
	{
        $id        = (int) $id;
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

        if ($check)
        {
            $check = Check::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id'                => $check,
                'check_category_id' => $category->id
            ));

            if (!$check)
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));
        }
        else
        {
            $check     = new Check();
            $newRecord = true;
        }

        $languages       = Language::model()->findAll();
        $defaultLanguage = Language::model()->findByAttributes(
            array( 'default' => true ),
            array( 'order'   => '"default" DESC' )
        );

        if ($defaultLanguage)
            $defaultLanguage = $defaultLanguage->id;

		$model = new CheckEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name              = $check->name;
            $model->backgroundInfo    = $check->background_info;
            $model->impactInfo        = $check->impact_info;
            $model->manualInfo        = $check->manual_info;
            $model->script            = $check->script;
            $model->advanced          = $check->advanced;
            $model->automated         = $check->automated;
            $model->protocol          = $check->protocol;
            $model->port              = $check->port;
            $model->multipleSolutions = $check->multiple_solutions;

            $checkL10n = CheckL10n::model()->findAllByAttributes(array(
                'check_id' => $check->id
            ));

            foreach ($checkL10n as $cl)
            {
                $i = array();

                $i['name']           = $cl->name;
                $i['backgroundInfo'] = $cl->background_info;
                $i['impactInfo']     = $cl->impact_info;
                $i['manualInfo']     = $cl->manual_info;

                $model->localizedItems[$cl->language_id] = $i;
            }
        }

		// collect user input data
		if (isset($_POST['CheckEditForm']))
		{
			$model->attributes = $_POST['CheckEditForm'];

            if (!isset($_POST['CheckEditForm']['advanced']))
                $model->advanced = false;

            if (!isset($_POST['CheckEditForm']['automated']))
                $model->automated = false;

            if (!isset($_POST['CheckEditForm']['multipleSolutions']))
                $model->multipleSolutions = false;

			if ($model->validate())
            {
                $check->check_category_id  = $category->id;
                $check->name               = $model->name;
                $check->background_info    = $model->backgroundInfo;
                $check->impact_info        = $model->impactInfo;
                $check->manual_info        = $model->manualInfo;
                $check->script             = $model->script;
                $check->advanced           = $model->advanced;
                $check->automated          = $model->automated;
                $check->multiple_solutions = $model->multipleSolutions;
                $check->protocol           = $model->protocol;
                $check->port               = $model->port;

                $check->save();

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

                    $checkL10n->name            = $value['name'];
                    $checkL10n->background_info = $value['backgroundInfo'];
                    $checkL10n->impact_info     = $value['impactInfo'];
                    $checkL10n->manual_info     = $value['manualInfo'];
                    $checkL10n->save();
                }

                $targetCheckCategories = TargetCheckCategory::model()->findAllByAttributes(array(
                    'check_category_id' => $category->id
                ));

                foreach ($targetCheckCategories as $targetCheckCategory)
                    $targetCheckCategory->updateStats();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Check saved.'));

                $check->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/editcheck', 'id' => $category->id, 'check' => $check->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName] = $this->createUrl('check/view', array( 'id' => $category->id ));

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Check')] = '';
        else
            $this->breadcrumbs[$check->localizedName] = '';

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Check') : $check->localizedName;
		$this->render('check/edit', array(
            'model'           => $model,
            'category'        => $category,
            'check'           => $check,
            'languages'       => $languages,
            'defaultLanguage' => $defaultLanguage
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
	public function actionResults($id, $check, $page=1)
	{
        $id    = (int) $id;
        $check = (int) $check;
        $page  = (int) $page;

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

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $check,
            'check_category_id' => $category->id
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

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName] = $this->createUrl('check/view', array( 'id' => $category->id ));
        $this->breadcrumbs[$check->localizedName]    = $this->createUrl('check/editcheck', array( 'id' => $category->id, 'check' => $check->id ));
        $this->breadcrumbs[Yii::t('app', 'Results')] = '';

        // display the page
        $this->pageTitle = $check->localizedName;
		$this->render('check/result/index', array(
            'results'  => $check_results,
            'p'        => $paginator,
            'check'    => $check,
            'category' => $category
        ));
	}

    /**
     * Check result edit page.
     */
	public function actionEditResult($id, $check, $result=0)
	{
        $id        = (int) $id;
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

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $check,
            'check_category_id' => $category->id
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

        $languages       = Language::model()->findAll();
        $defaultLanguage = Language::model()->findByAttributes(
            array( 'default' => true ),
            array( 'order'   => '"default" DESC' )
        );

        if ($defaultLanguage)
            $defaultLanguage = $defaultLanguage->id;

		$model = new CheckResultEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->result    = $result->result;
            $model->sortOrder = $result->sort_order;

            $checkResultL10n = CheckResultL10n::model()->findAllByAttributes(array(
                'check_result_id' => $result->id
            ));

            foreach ($checkResultL10n as $crl)
                $model->localizedItems[$crl->language_id]['result'] = $crl->result;
        }

		// collect user input data
		if (isset($_POST['CheckResultEditForm']))
		{
			$model->attributes = $_POST['CheckResultEditForm'];

			if ($model->validate())
            {
                $result->check_id   = $check->id;
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

                    $checkResultL10n->result = $value['result'];
                    $checkResultL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Result saved.'));

                $result->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/editresult', 'id' => $category->id, 'check' => $check->id, 'result' => $result->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName] = $this->createUrl('check/view', array( 'id' => $category->id ));
        $this->breadcrumbs[$check->localizedName]    = $this->createUrl('check/editcheck', array( 'id' => $category->id, 'check' => $check->id ));
        $this->breadcrumbs[Yii::t('app', 'Results')] = $this->createUrl('check/results', array( 'id' => $category->id, 'check' => $check->id ));

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Result')] = '';
        else
            $this->breadcrumbs[$result->localizedResult] = '';

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Result') : $result->localizedResult;
		$this->render('check/result/edit', array(
            'model'           => $model,
            'category'        => $category,
            'check'           => $check,
            'result'          => $result,
            'languages'       => $languages,
            'defaultLanguage' => $defaultLanguage
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
	public function actionSolutions($id, $check, $page=1)
	{
        $id    = (int) $id;
        $check = (int) $check;
        $page  = (int) $page;

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

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $check,
            'check_category_id' => $category->id
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

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName] = $this->createUrl('check/view', array( 'id' => $category->id ));
        $this->breadcrumbs[$check->localizedName]    = $this->createUrl('check/editcheck', array( 'id' => $category->id, 'check' => $check->id ));
        $this->breadcrumbs[Yii::t('app', 'Solutions')] = '';

        // display the page
        $this->pageTitle = $check->localizedName;
		$this->render('check/solution/index', array(
            'solutions' => $check_solutions,
            'p'         => $paginator,
            'check'     => $check,
            'category'  => $category
        ));
	}

    /**
     * Check solution edit page.
     */
	public function actionEditSolution($id, $check, $solution=0)
	{
        $id        = (int) $id;
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

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $check,
            'check_category_id' => $category->id
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

        $languages       = Language::model()->findAll();
        $defaultLanguage = Language::model()->findByAttributes(
            array( 'default' => true ),
            array( 'order'   => '"default" DESC' )
        );

        if ($defaultLanguage)
            $defaultLanguage = $defaultLanguage->id;

		$model = new CheckSolutionEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->solution  = $solution->solution;
            $model->sortOrder = $solution->sort_order;

            $checkSolutionL10n = CheckSolutionL10n::model()->findAllByAttributes(array(
                'check_solution_id' => $solution->id
            ));

            foreach ($checkSolutionL10n as $csl)
                $model->localizedItems[$csl->language_id]['solution'] = $csl->solution;
        }

		// collect user input data
		if (isset($_POST['CheckSolutionEditForm']))
		{
			$model->attributes = $_POST['CheckSolutionEditForm'];

			if ($model->validate())
            {
                $solution->check_id   = $check->id;
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

                    $checkSolutionL10n->solution = $value['solution'];
                    $checkSolutionL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Solution saved.'));

                $solution->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/editsolution', 'id' => $category->id, 'check' => $check->id, 'solution' => $solution->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[Yii::t('app', 'Checks')]    = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName]   = $this->createUrl('check/view', array( 'id' => $category->id ));
        $this->breadcrumbs[$check->localizedName]      = $this->createUrl('check/editcheck', array( 'id' => $category->id, 'check' => $check->id ));
        $this->breadcrumbs[Yii::t('app', 'Solutions')] = $this->createUrl('check/solutions', array( 'id' => $category->id, 'check' => $check->id ));

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Solution')] = '';
        else
            $this->breadcrumbs[$solution->localizedSolution] = '';

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Solution') : $solution->localizedSolution;
		$this->render('check/solution/edit', array(
            'model'           => $model,
            'category'        => $category,
            'check'           => $check,
            'solution'        => $solution,
            'languages'       => $languages,
            'defaultLanguage' => $defaultLanguage
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
	public function actionInputs($id, $check, $page=1)
	{
        $id    = (int) $id;
        $check = (int) $check;
        $page  = (int) $page;

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

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $check,
            'check_category_id' => $category->id
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

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName] = $this->createUrl('check/view', array( 'id' => $category->id ));
        $this->breadcrumbs[$check->localizedName]    = $this->createUrl('check/editcheck', array( 'id' => $category->id, 'check' => $check->id ));
        $this->breadcrumbs[Yii::t('app', 'Inputs')] = '';

        // display the page
        $this->pageTitle = $check->localizedName;
		$this->render('check/input/index', array(
            'inputs'   => $check_inputs,
            'p'        => $paginator,
            'check'    => $check,
            'category' => $category
        ));
	}

    /**
     * Check input edit page.
     */
	public function actionEditInput($id, $check, $input=0)
	{
        $id        = (int) $id;
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

        $check = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByAttributes(array(
            'id'                => $check,
            'check_category_id' => $category->id
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

        $languages       = Language::model()->findAll();
        $defaultLanguage = Language::model()->findByAttributes(
            array( 'default' => true ),
            array( 'order'   => '"default" DESC' )
        );

        if ($defaultLanguage)
            $defaultLanguage = $defaultLanguage->id;

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

		// collect user input data
		if (isset($_POST['CheckInputEditForm']))
		{
			$model->attributes = $_POST['CheckInputEditForm'];

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

                    $checkInputL10n->name        = $value['name'];
                    $checkInputL10n->description = $value['description'];
                    $checkInputL10n->value       = $value['value'];
                    $checkInputL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Input saved.'));

                $input->refresh();

                if ($newRecord)
                    $this->redirect(array( 'check/editinput', 'id' => $category->id, 'check' => $check->id, 'input' => $input->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[Yii::t('app', 'Checks')]  = $this->createUrl('check/index');
        $this->breadcrumbs[$category->localizedName] = $this->createUrl('check/view', array( 'id' => $category->id ));
        $this->breadcrumbs[$check->localizedName]    = $this->createUrl('check/editcheck', array( 'id' => $category->id, 'check' => $check->id ));
        $this->breadcrumbs[Yii::t('app', 'Inputs')]  = $this->createUrl('check/inputs', array( 'id' => $category->id, 'check' => $check->id ));

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Input')] = '';
        else
            $this->breadcrumbs[$input->localizedName] = '';

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Input') : $input->localizedName;
		$this->render('check/input/edit', array(
            'model'           => $model,
            'category'        => $category,
            'check'           => $check,
            'input'           => $input,
            'languages'       => $languages,
            'defaultLanguage' => $defaultLanguage
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
}
