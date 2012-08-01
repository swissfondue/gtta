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
			'checkAuth',
            'checkAdmin',
		);
	}

    /**
     * Display a list of risk categories.
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

        $risks = RiskCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $riskCount = RiskCategory::model()->count($criteria);
        $paginator = new Paginator($riskCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Categories'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Risk Categories');
		$this->render('index', array(
            'risks' => $risks,
            'p'     => $paginator
        ));
	}

    /**
     * Risk edit page.
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

            $risk = RiskCategory::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByPk($id);
        }
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
                $risk->name = $model->name;
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

                Yii::app()->user->setFlash('success', Yii::t('app', 'Risk category saved.'));

                $risk->refresh();

                if ($newRecord)
                    $this->redirect(array( 'risk/edit', 'id' => $risk->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Categories'), $this->createUrl('risk/index'));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Risk Category'), '');
        else
            $this->breadcrumbs[] = array($risk->localizedName, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Risk Category') : $risk->localizedName;
		$this->render('edit', array(
            'model'     => $model,
            'risk'      => $risk,
            'languages' => $languages,
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

            $id   = $model->id;
            $risk = RiskCategory::model()->findByPk($id);

            if ($risk === null)
                throw new CHttpException(404, Yii::t('app', 'Risk Category not found.'));

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