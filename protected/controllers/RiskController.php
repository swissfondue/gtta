<?php

/**
 * Risk controller.
 */
class RiskController extends Controller
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
            "idle",
		);
	}

    /**
     * Display a list of risk templates.
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
        $criteria->order  = 'COALESCE(l10n.name, t.name) ASC';
        $criteria->together = true;

        $templates = RiskTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $templateCount = RiskTemplate::model()->count($criteria);
        $paginator     = new Paginator($templateCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Matrix Templates'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Risk Matrix Templates');
		$this->render('index', array(
            'templates' => $templates,
            'p'         => $paginator
        ));
	}

    /**
     * Template edit page.
     */
	public function actionEdit($id=0)
	{
        $id        = (int) $id;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        if ($id)
            $template = RiskTemplate::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByPk($id);
        else
        {
            $template  = new RiskTemplate();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new RiskTemplateEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name = $template->name;

            $templateL10n = RiskTemplateL10n::model()->findAllByAttributes(array(
                'risk_template_id' => $template->id
            ));

            foreach ($templateL10n as $tl)
                $model->localizedItems[$tl->language_id]['name'] = $tl->name;
        }

		// collect user input data
		if (isset($_POST['RiskTemplateEditForm']))
		{
			$model->attributes = $_POST['RiskTemplateEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

			if ($model->validate())
            {
                $template->name = $model->name;
                $template->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $templateL10n = RiskTemplateL10n::model()->findByAttributes(array(
                        'risk_template_id' => $template->id,
                        'language_id'      => $languageId
                    ));

                    if (!$templateL10n)
                    {
                        $templateL10n = new RiskTemplateL10n();
                        $templateL10n->risk_template_id = $template->id;
                        $templateL10n->language_id      = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = NULL;

                    $templateL10n->name = $value['name'];
                    $templateL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Template saved.'));

                $template->refresh();

                if ($newRecord) {
                    $this->redirect(array( 'risk/edit', 'id' => $template->id ));
                }
                
                // refresh the template
                $template = RiskTemplate::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findByPk($id);
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Matrix Templates'), $this->createUrl('risk/index'));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Template'), '');
        else
        {
            $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('risk/view', array( 'id' => $template->id )));
            $this->breadcrumbs[] = array(Yii::t('app', 'Edit'), '');
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Template') : $template->localizedName;
		$this->render('edit', array(
            'model'      => $model,
            'template'   => $template,
            'languages'  => $languages,
        ));
	}

    /**
     * Control risk template.
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

            $id = $model->id;
            $template = RiskTemplate::model()->findByPk($id);

            if ($template === null)
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $template->delete();
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
     * Display a list of risk categories.
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

        $template = RiskTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$template)
            throw new CHttpException(404, Yii::t('app', 'Template not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 'COALESCE(l10n.name, t.name) ASC';
        $criteria->addColumnCondition(array( 'risk_template_id' => $template->id ));
        $criteria->together = true;

        $risks = RiskCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $riskCount = RiskCategory::model()->count($criteria);
        $paginator = new Paginator($riskCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Matrix Templates'), $this->createUrl('risk/index'));
        $this->breadcrumbs[] = array($template->localizedName, '');

        // display the page
        $this->pageTitle = $template->localizedName;
		$this->render('view', array(
            'risks'    => $risks,
            'p'        => $paginator,
            'template' => $template
        ));
	}

    /**
     * Category edit page.
     */
	public function actionEditCategory($id, $category=0)
	{
        $id        = (int) $id;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $template = RiskTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$template)
            throw new CHttpException(404, Yii::t('app', 'Template not found.'));

        if ($category)
            $risk = RiskCategory::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id'               => $category,
                'risk_template_id' => $template->id
            ));
        else
        {
            $risk      = new RiskCategory();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new RiskCategoryEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name = $risk->name;

            $riskL10n = RiskCategoryL10n::model()->findAllByAttributes(array(
                'risk_category_id' => $risk->id
            ));

            foreach ($riskL10n as $rl)
                $model->localizedItems[$rl->language_id]['name'] = $rl->name;
        }

		// collect user input data
		if (isset($_POST['RiskCategoryEditForm']))
		{
			$model->attributes = $_POST['RiskCategoryEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');

			if ($model->validate())
            {
                $risk->risk_template_id = $template->id;
                $risk->name             = $model->name;
                $risk->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $riskL10n = RiskCategoryL10n::model()->findByAttributes(array(
                        'risk_category_id' => $risk->id,
                        'language_id'      => $languageId
                    ));

                    if (!$riskL10n)
                    {
                        $riskL10n = new RiskCategoryL10n();
                        $riskL10n->risk_category_id = $risk->id;
                        $riskL10n->language_id      = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = NULL;

                    $riskL10n->name = $value['name'];
                    $riskL10n->save();
                }

                foreach ($model->checks as $checkId => $value)
                {
                    $riskCategoryCheck = RiskCategoryCheck::model()->findByAttributes(array(
                        'risk_category_id' => $risk->id,
                        'check_id'         => $checkId
                    ));

                    if (!$riskCategoryCheck)
                    {
                        $riskCategoryCheck = new RiskCategoryCheck();
                        $riskCategoryCheck->risk_category_id = $risk->id;
                        $riskCategoryCheck->check_id         = $checkId;
                    }

                    $riskCategoryCheck->damage     = $value['damage'];
                    $riskCategoryCheck->likelihood = $value['likelihood'];
                    $riskCategoryCheck->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Category saved.'));

                $risk->refresh();

                if ($newRecord) {
                    $this->redirect(array( 'risk/editcategory', 'id' => $template->id, 'category' => $risk->id ));
                }

                // refresh the category
                $risk = RiskCategory::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findByAttributes(array(
                    "id" => $category,
                    "risk_template_id" => $template->id
                ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'l10n.language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),

            'controls' => array(
                'joinType' => 'LEFT JOIN',
                'order'    => 'COALESCE(l10n_controls.name, controls.name) ASC',

                'with' => array(
                    'l10n' => array(
                        'alias'    => 'l10n_controls',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n_controls.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    ),

                    'checks' => array(
                        'joinType' => 'LEFT JOIN',
                        'order'    => 'COALESCE(l10n_checks.name, checks.name) ASC',

                        'with' => array(
                            'l10n' => array(
                                'alias'    => 'l10n_checks',
                                'joinType' => 'LEFT JOIN',
                                'on'       => 'l10n_checks.language_id = :language_id',
                                'params'   => array( 'language_id' => $language )
                            ),

                            'riskCategories' => array(
                                'joinType' => 'LEFT JOIN',
                                'on'       => '"riskCategories".risk_category_id = :risk_category_id',
                                'params'   => array( 'risk_category_id' => $category )
                            )
                        )
                    )
                )
            )
        ))->findAllByAttributes(
            array(),
            array( 'order' => 'COALESCE(l10n.name, t.name) ASC' )
        );

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Matrix Templates'), $this->createUrl('risk/index'));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('risk/view', array( 'id' => $template->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Category'), '');
        else
            $this->breadcrumbs[] = array($risk->localizedName, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Category') : $risk->localizedName;
		$this->render('category/edit', array(
            'model'      => $model,
            'risk'       => $risk,
            'languages'  => $languages,
            'categories' => $categories,
            'template'   => $template
        ));
	}

    /**
     * Control risk category.
     */
    public function actionControlCategory($id)
    {
        $response = new AjaxResponse();

        try
        {
            $id = (int) $id;

            $template = RiskTemplate::model()->findByPk($id);

            if (!$template)
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));

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

            $id   = $model->id;
            $risk = RiskCategory::model()->findByAttributes(array(
                'id'               => $id,
                'risk_template_id' => $template->id,
            ));

            if ($risk === null)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $risk->delete();
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