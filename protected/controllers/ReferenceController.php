<?php

/**
 * Reference controller.
 */
class ReferenceController extends Controller
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
     * Display a list of references.
     */
	public function actionIndex($page=1)
	{
        $page = (int) $page;

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->order  = 't.name ASC';

        $references = Reference::model()->findAll($criteria);

        $referenceCount = Reference::model()->count($criteria);
        $paginator      = new Paginator($referenceCount, $page);

        $this->breadcrumbs[] = array(Yii::t('app', 'References'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'References');
		$this->render('index', array(
            'references' => $references,
            'p'          => $paginator
        ));
	}

    /**
     * Reference edit page.
     */
	public function actionEdit($id=0)
	{
        $id        = (int) $id;
        $newRecord = false;

        if ($id)
            $reference = Reference::model()->findByPk($id);
        else
        {
            $reference = new Reference();
            $newRecord = true;
        }

		$model = new ReferenceEditForm();

        if (!$newRecord)
        {
            $model->name = $reference->name;
            $model->url  = $reference->url;
        }

		// collect reference input data
		if (isset($_POST['ReferenceEditForm']))
		{
			$model->attributes = $_POST['ReferenceEditForm'];

			if ($model->validate())
            {
                $reference->name = $model->name;
                $reference->url  = $model->url;

                $reference->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Reference saved.'));

                $reference->refresh();

                if ($newRecord)
                    $this->redirect(array( 'reference/edit', 'id' => $reference->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'References'), $this->createUrl('reference/index'));

        if ($newRecord)
            $this->breadcrumbs[] = array(Yii::t('app', 'New Reference'), '');
        else
            $this->breadcrumbs[] = array($reference->name, '');

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Reference') : $reference->name;
		$this->render('edit', array(
            'model'     => $model,
            'reference' => $reference,
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

            $id        = $model->id;
            $reference = Reference::model()->findByPk($id);

            if ($reference === null)
                throw new CHttpException(404, Yii::t('app', 'Reference not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $reference->delete();
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