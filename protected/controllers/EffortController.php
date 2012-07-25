<?php

/**
 * Effort controller.
 */
class EffortController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'checkAuth',
		);
	}

    /**
     * Display an effort form.
     */
	public function actionIndex()
	{
        $references = Reference::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

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

        $checks = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),
            'control'
        ))->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $referenceArray = array();
        $checkArray     = array();

        foreach ($references as $reference)
            $referenceArray[] = array(
                'id'   => $reference->id,
                'name' => $reference->name
            );

        foreach ($categories as $category)
        {
            $checkCategory = array(
                'id'     => $category->id,
                'name'   => $category->localizedName,
                'checks' => array()
            );

            foreach ($checks as $check)
                if ($check->control->check_category_id == $category->id)
                    $checkCategory['checks'][] = array(
                        'effort'    => $check->effort,
                        'advanced'  => $check->advanced,
                        'reference' => $check->reference_id
                    );

            $checkArray[] = $checkCategory;
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Effort Estimation'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Effort Estimation');
		$this->render('index', array(
            'references' => $referenceArray,
            'checks'     => $checkArray,
        ));
    }
}