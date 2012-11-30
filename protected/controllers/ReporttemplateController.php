<?php

/**
 * Report template controller.
 */
class ReporttemplateController extends Controller
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
            'ajaxOnly + controlheaderimage, controlsummary, controlsection',
            'postOnly + uploadheaderimage, controlheaderimage, controlsummary, controlsection',
		);
	}

    /**
     * Display a list of report templates.
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

        $templates = ReportTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $templateCount = ReportTemplate::model()->count($criteria);
        $paginator     = new Paginator($templateCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Report Templates'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Report Templates');
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
            $template = ReportTemplate::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByPk($id);
        else
        {
            $template  = new ReportTemplate();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new ReportTemplateEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->name = $template->name;
            $model->intro = $template->intro;
            $model->appendix = $template->appendix;
            $model->vulnsIntro = $template->vulns_intro;
            $model->infoChecksIntro = $template->info_checks_intro;
            $model->securityLevelIntro = $template->security_level_intro;
            $model->vulnDistributionIntro = $template->vuln_distribution_intro;

            $templateL10n = ReportTemplateL10n::model()->findAllByAttributes(array(
                'report_template_id' => $template->id
            ));

            foreach ($templateL10n as $tl)
            {
                $model->localizedItems[$tl->language_id]['name'] = $tl->name;
                $model->localizedItems[$tl->language_id]['intro'] = $tl->intro;
                $model->localizedItems[$tl->language_id]['appendix'] = $tl->appendix;
                $model->localizedItems[$tl->language_id]['vulnsIntro'] = $tl->vulns_intro;
                $model->localizedItems[$tl->language_id]['infoChecksIntro'] = $tl->info_checks_intro;
                $model->localizedItems[$tl->language_id]['securityLevelIntro'] = $tl->security_level_intro;
                $model->localizedItems[$tl->language_id]['vulnDistributionIntro'] = $tl->vuln_distribution_intro;
            }
        }

		// collect user input data
		if (isset($_POST['ReportTemplateEditForm']))
		{
			$model->attributes = $_POST['ReportTemplateEditForm'];
            $model->name = $model->defaultL10n($languages, 'name');
            $model->intro = $model->defaultL10n($languages, 'intro');
            $model->appendix = $model->defaultL10n($languages, 'appendix');
            $model->vulnsIntro = $model->defaultL10n($languages, 'vulnsIntro');
            $model->infoChecksIntro = $model->defaultL10n($languages, 'infoChecksIntro');
            $model->securityLevelIntro = $model->defaultL10n($languages, 'securityLevelIntro');
            $model->vulnDistributionIntro = $model->defaultL10n($languages, 'vulnDistributionIntro');

			if ($model->validate())
            {
                $template->name = $model->name;
                $template->intro = $model->intro;
                $template->appendix = $model->appendix;
                $template->vulns_intro = $model->vulnsIntro;
                $template->info_checks_intro = $model->infoChecksIntro;
                $template->security_level_intro = $model->securityLevelIntro;
                $template->vuln_distribution_intro = $model->vulnDistributionIntro;
                $template->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $templateL10n = ReportTemplateL10n::model()->findByAttributes(array(
                        'report_template_id' => $template->id,
                        'language_id'        => $languageId
                    ));

                    if (!$templateL10n)
                    {
                        $templateL10n = new ReportTemplateL10n();
                        $templateL10n->report_template_id = $template->id;
                        $templateL10n->language_id      = $languageId;
                    }

                    if ($value['name'] == '')
                        $value['name'] = NULL;

                    if ($value['intro'] == '')
                        $value['intro'] = NULL;

                    if ($value['appendix'] == '')
                        $value['appendix'] = NULL;

                    if ($value['vulnsIntro'] == '')
                        $value['vulnsIntro'] = NULL;

                    if ($value['infoChecksIntro'] == '')
                        $value['infoChecksIntro'] = NULL;

                    if ($value['securityLevelIntro'] == '')
                        $value['securityLevelIntro'] = NULL;

                    if ($value['vulnDistributionIntro'] == '')
                        $value['vulnDistributionIntro'] = NULL;

                    $templateL10n->name = $value['name'];
                    $templateL10n->intro = $value['intro'];
                    $templateL10n->appendix = $value['appendix'];
                    $templateL10n->vulns_intro = $value['vulnsIntro'];
                    $templateL10n->info_checks_intro = $value['infoChecksIntro'];
                    $templateL10n->security_level_intro = $value['securityLevelIntro'];
                    $templateL10n->vuln_distribution_intro = $value['vulnDistributionIntro'];

                    $templateL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Template saved.'));

                $template->refresh();

                if ($newRecord)
                    $this->redirect(array( 'reporttemplate/edit', 'id' => $template->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Report Templates'), $this->createUrl('reporttemplate/index'));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Template'), '');
        else
            $this->breadcrumbs[] = array($template->localizedName, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Template') : $template->localizedName;
		$this->render('edit', array(
            'model'      => $model,
            'template'   => $template,
            'languages'  => $languages,
        ));
	}

    /**
     * Control report template.
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
            $template = ReportTemplate::model()->findByPk($id);

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
     * Upload header image function.
     */
    function actionUploadHeaderImage($id)
    {
        $response = new AjaxResponse();

        try
        {
            $id = (int) $id;

            $template = ReportTemplate::model()->findByPk($id);

            if (!$template)
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));

            $model = new ReportTemplateHeaderImageUploadForm();
            $model->image = CUploadedFile::getInstanceByName('ReportTemplateHeaderImageUploadForm[image]');

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

            // delete the old image
            if ($template->header_image_path)
                @unlink(Yii::app()->params['reports']['headerImages']['path'] . '/' . $template->header_image_path);

            $template->header_image_type = $model->image->type;
            $template->header_image_path = hash('sha256', $model->image->name . rand() . time());
            $template->save();

            $model->image->saveAs(Yii::app()->params['reports']['headerImages']['path'] . '/' . $template->header_image_path);

            $response->addData('url', $this->createUrl('reporttemplate/headerimage', array( 'id' => $template->id )));
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control header image.
     */
    public function actionControlHeaderImage()
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

            $template = ReportTemplate::model()->findByPk($model->id);

            if ($template === null)
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));

            if (!$template->header_image_path)
                throw new CHttpException(404, Yii::t('app', 'Header image not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    @unlink(Yii::app()->params['reports']['headerImages']['path'] . '/' . $template->header_image_path);
                    $template->header_image_path = NULL;
                    $template->header_image_type = NULL;
                    $template->save();

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
     * Get header image.
     */
    public function actionHeaderImage($id)
    {
        $id = (int) $id;

        $template = ReportTemplate::model()->findByPk($id);

        if ($template === null)
            throw new CHttpException(404, Yii::t('app', 'Template not found.'));

        if (!$template->header_image_path)
            throw new CHttpException(404, Yii::t('app', 'Header image not found.'));

        $filePath = Yii::app()->params['reports']['headerImages']['path'] . '/' . $template->header_image_path;

        if (!file_exists($filePath))
            throw new CHttpException(404, Yii::t('app', 'Header image not found.'));

        $extension = 'jpg';

        if ($template->header_image_type == 'image/png')
            $extension = 'png';

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $template->header_image_type);
        header('Content-Disposition: attachment; filename="header-image.' . $extension . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Display a list of summary blocks.
     */
	public function actionSummary($id, $page=1)
	{
        $id = (int) $id;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $template = ReportTemplate::model()->with(array(
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
        $criteria->order  = 't.rating_from ASC';
        $criteria->addColumnCondition(array( 'report_template_id' => $template->id ));

        $summary_blocks = ReportTemplateSummary::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $blockCount = ReportTemplateSummary::model()->count($criteria);
        $paginator = new Paginator($blockCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Report Templates'), $this->createUrl('reporttemplate/index'));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Summary Blocks'), '');

        // display the page
        $this->pageTitle = $template->localizedName;
		$this->render('summary/index', array(
            'summaryBlocks' => $summary_blocks,
            'p'             => $paginator,
            'template'      => $template,
        ));
	}

    /**
     * Summary block edit page.
     */
	public function actionEditSummary($id, $summary=0)
	{
        $id = (int) $id;
        $summary = (int) $summary;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $template = ReportTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$template)
            throw new CHttpException(404, Yii::t('app', 'Template not found.'));

        if ($summary)
        {
            $summary = ReportTemplateSummary::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id' => $summary,
                'report_template_id' => $template->id
            ));

            if (!$summary)
                throw new CHttpException(404, Yii::t('app', 'Summary block not found.'));
        }
        else
        {
            $summary = new ReportTemplateSummary();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new ReportTemplateSummaryEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->title      = $summary->title;
            $model->summary    = $summary->summary;
            $model->ratingFrom = $summary->rating_from;
            $model->ratingTo   = $summary->rating_to;

            $reportTemplateSummaryL10n = ReportTemplateSummaryL10n::model()->findAllByAttributes(array(
                'report_template_summary_id' => $summary->id
            ));

            foreach ($reportTemplateSummaryL10n as $rtsl)
            {
                $model->localizedItems[$rtsl->language_id]['title']   = $rtsl->title;
                $model->localizedItems[$rtsl->language_id]['summary'] = $rtsl->summary;
            }
        }

		// collect user input data
		if (isset($_POST['ReportTemplateSummaryEditForm']))
		{
			$model->attributes = $_POST['ReportTemplateSummaryEditForm'];
            $model->title   = $model->defaultL10n($languages, 'title');
            $model->summary = $model->defaultL10n($languages, 'summary');

			if ($model->validate())
            {
                $summary->report_template_id = $template->id;
                $summary->title = $model->title;
                $summary->summary = $model->summary;
                $summary->rating_from = $model->ratingFrom;
                $summary->rating_to = $model->ratingTo;
                $summary->save();

                foreach ($model->localizedItems as $languageId => $value)
                {
                    $reportTemplateSummaryL10n = ReportTemplateSummaryL10n::model()->findByAttributes(array(
                        'report_template_summary_id' => $summary->id,
                        'language_id' => $languageId
                    ));

                    if (!$reportTemplateSummaryL10n)
                    {
                        $reportTemplateSummaryL10n = new ReportTemplateSummaryL10n();
                        $reportTemplateSummaryL10n->report_template_summary_id = $summary->id;
                        $reportTemplateSummaryL10n->language_id = $languageId;
                    }

                    if ($value['summary'] == '')
                        $value['summary'] = NULL;

                    if ($value['title'] == '')
                        $value['title'] = NULL;

                    $reportTemplateSummaryL10n->title = $value['title'];
                    $reportTemplateSummaryL10n->summary = $value['summary'];
                    $reportTemplateSummaryL10n->save();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Summary block saved.'));

                $summary->refresh();

                if ($newRecord)
                    $this->redirect(array( 'reporttemplate/editsummary', 'id' => $template->id, 'summary' => $summary->id ));
            }

            if (count($model->getErrors()) > 0)
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Report Templates'), $this->createUrl('reporttemplate/index'));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Summary Blocks'), $this->createUrl('reporttemplate/summary', array( 'id' => $template->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Summary Block'), '');
        else
            $this->breadcrumbs[] = array($summary->localizedTitle, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Summary Block') : $summary->localizedTitle;
		$this->render('summary/edit', array(
            'model'     => $model,
            'template'  => $template,
            'summary'   => $summary,
            'languages' => $languages,
        ));
	}

    /**
     * Summary block control function.
     */
    public function actionControlSummary()
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
            $summary = ReportTemplateSummary::model()->findByPk($id);

            if ($summary === null)
                throw new CHttpException(404, Yii::t('app', 'Summary block not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $summary->delete();
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
     * Display a list of vulnerability sections.
     */
	public function actionSections($id, $page=1)
	{
        $id = (int) $id;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $template = ReportTemplate::model()->with(array(
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
        $criteria->order  = 't.sort_order ASC';
        $criteria->addColumnCondition(array( 'report_template_id' => $template->id ));

        $sections = ReportTemplateSection::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $blockCount = ReportTemplateSection::model()->count($criteria);
        $paginator = new Paginator($blockCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'Report Templates'), $this->createUrl('reporttemplate/index'));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Sections'), '');

        // display the page
        $this->pageTitle = $template->localizedName;
		$this->render('section/index', array(
            'sections' => $sections,
            'p'        => $paginator,
            'template' => $template,
        ));
	}

    /**
     * Section edit page.
     */
	public function actionEditSection($id, $section=0)
	{
        $id = (int) $id;
        $section = (int) $section;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $template = ReportTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findByPk($id);

        if (!$template)
            throw new CHttpException(404, Yii::t('app', 'Template not found.'));

        if ($section)
        {
            $section = ReportTemplateSection::model()->with(array(
                'l10n' => array(
                    'joinType' => 'LEFT JOIN',
                    'on'       => 'language_id = :language_id',
                    'params'   => array( 'language_id' => $language )
                )
            ))->findByAttributes(array(
                'id' => $section,
                'report_template_id' => $template->id
            ));

            if (!$section)
                throw new CHttpException(404, Yii::t('app', 'Section not found.'));
        }
        else
        {
            $section = new ReportTemplateSection();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new ReportTemplateSectionEditForm();
        $model->localizedItems = array();

        if (!$newRecord)
        {
            $model->intro      = $section->intro;
            $model->title      = $section->title;
            $model->categoryId = $section->check_category_id;
            $model->sortOrder  = $section->sort_order;
            $model->categoryId = $section->check_category_id;

            $reportTemplateSectionL10n = ReportTemplateSectionL10n::model()->findAllByAttributes(array(
                'report_template_section_id' => $section->id
            ));

            foreach ($reportTemplateSectionL10n as $rtsl)
            {
                $model->localizedItems[$rtsl->language_id]['intro'] = $rtsl->intro;
                $model->localizedItems[$rtsl->language_id]['title'] = $rtsl->title;
            }
        }
        else
        {
            // increment last sort_order, if any
            $criteria = new CDbCriteria();
            $criteria->select = 'MAX(sort_order) as max_sort_order';
            $criteria->addColumnCondition(array( 'report_template_id' => $template->id ));

            $maxOrder = ReportTemplateSection::model()->find($criteria);

            if ($maxOrder && $maxOrder->max_sort_order !== NULL)
                $model->sortOrder = $maxOrder->max_sort_order + 1;
        }

		// collect user input data
		if (isset($_POST['ReportTemplateSectionEditForm']))
		{
			$model->attributes = $_POST['ReportTemplateSectionEditForm'];
            $model->intro = $model->defaultL10n($languages, 'intro');
            $model->title = $model->defaultL10n($languages, 'title');

			if ($model->validate())
            {
                $check = ReportTemplateSection::model()->findByAttributes(array(
                    'report_template_id' => $template->id,
                    'check_category_id'  => $model->categoryId
                ));

                if ($check)
                    $model->addError('categoryId', Yii::t('app', 'Section with this category already exists.'));
                else
                {
                    $section->report_template_id = $template->id;
                    $section->intro = $model->intro;
                    $section->title = $model->title;
                    $section->sort_order = $model->sortOrder;
                    $section->check_category_id = $model->categoryId;
                    $section->save();

                    foreach ($model->localizedItems as $languageId => $value)
                    {
                        $reportTemplateSectionL10n = ReportTemplateSectionL10n::model()->findByAttributes(array(
                            'report_template_section_id' => $section->id,
                            'language_id' => $languageId
                        ));

                        if (!$reportTemplateSectionL10n)
                        {
                            $reportTemplateSectionL10n = new ReportTemplateSectionL10n();
                            $reportTemplateSectionL10n->report_template_section_id = $section->id;
                            $reportTemplateSectionL10n->language_id = $languageId;
                        }

                        if ($value['title'] == '')
                            $value['title'] = NULL;

                        if ($value['intro'] == '')
                            $value['intro'] = NULL;

                        $reportTemplateSectionL10n->intro = $value['intro'];
                        $reportTemplateSectionL10n->title = $value['title'];
                        $reportTemplateSectionL10n->save();
                    }

                    Yii::app()->user->setFlash('success', Yii::t('app', 'Section saved.'));

                    $section->refresh();

                    if ($newRecord)
                        $this->redirect(array( 'reporttemplate/editsection', 'id' => $template->id, 'section' => $section->id ));
                }
            }

            if (count($model->getErrors()) > 0)
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $criteria = new CDbCriteria();
        $criteria->order = 'COALESCE(l10n.name, t.name) ASC';
        $criteria->together = true;

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Report Templates'), $this->createUrl('reporttemplate/index'));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )));
        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Sections'), $this->createUrl('reporttemplate/sections', array( 'id' => $template->id )));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Section'), '');
        else
            $this->breadcrumbs[] = array($section->localizedTitle, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Section') : $section->localizedTitle;
		$this->render('section/edit', array(
            'model'      => $model,
            'template'   => $template,
            'section'    => $section,
            'languages'  => $languages,
            'categories' => $categories,
        ));
	}

    /**
     * Section control function.
     */
    public function actionControlSection()
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
            $section = ReportTemplateSection::model()->findByPk($id);

            if ($section === null)
                throw new CHttpException(404, Yii::t('app', 'Section not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $section->delete();
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