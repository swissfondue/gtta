<?php

/**
 * Settings controller.
 */
class SettingsController extends Controller
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
     * Edit settings
     */
	public function actionEdit()
	{
        $model = new SettingsEditForm();
        $system = System::model()->findByPk(1);
        $model->timezone = $system->timezone;

        // collect form input data
		if (isset($_POST['SettingsEditForm'])) {
			$model->attributes = $_POST['SettingsEditForm'];

			if ($model->validate()) {
                $system->timezone = $model->timezone;
                $system->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Settings saved.'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
		}

        $this->breadcrumbs[] = array(Yii::t('app', 'Settings'), '');

		// display the page
        $this->pageTitle = Yii::t('app', 'Settings');
		$this->render('edit', array(
            'model' => $model,
            'system' => $system
        ));
    }
}