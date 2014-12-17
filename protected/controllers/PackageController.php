<?php

/**
 * Package controller.
 */
class PackageController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "checkAdmin",
            "ajaxOnly + control",
            "postOnly + control, upload",
            "idleOrPackageManager - index, regenerate, libraries, view, regeneratestatus",
            "idleOrRegenerate + regenerate, regeneratestatus",
            "idleOrRegenerateOrPackageManager + index, libraries, view",
		);
	}

    /**
     * Display a list of packages.
     */
	public function actionIndex($page=1) {
        $page = (int) $page;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->params["entriesPerPage"];
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];
        $criteria->addInCondition("status", array(
            Package::STATUS_INSTALL,
            Package::STATUS_INSTALLED,
            Package::STATUS_SHARE,
            Package::STATUS_ERROR
        ));
        $criteria->order = 't.type ASC, t.name ASC';

        $packages = Package::model()->findAll($criteria);

        $scriptCount = Package::model()->count($criteria);
        $paginator = new Paginator($scriptCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Packages");
		$this->render("index", array(
            "packages" => $packages,
            "p" => $paginator,
            "system" => $this->_system,
        ));
	}

    /**
     * New package
     */
    public function actionNew() {
        $model = new PackageAddForm();

		if (isset($_POST["PackageAddForm"])) {
			$model->attributes = $_POST["PackageAddForm"];

			if ($model->validate()) {
                try {
                    SystemManager::updateStatus(
                        System::STATUS_PACKAGE_MANAGER,
                        array(System::STATUS_IDLE, System::STATUS_PACKAGE_MANAGER)
                    );
                } catch (Exception $e) {
                    throw new CHttpException(403, Yii::t("app", "Access denied."));
                }

                $pm = new PackageManager();
                $pm->scheduleForInstallation($model->id);

                Yii::app()->user->setFlash("success", Yii::t("app", "Package scheduled for installation."));

                $this->redirect(array("package/index"));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "New Package"), "");
        $this->pageTitle = Yii::t("app", "New Package");

        // display the page
		$this->render("new", array(
            "model" => $model,
        ));
    }

    /**
     * Package control
     */
    public function actionControl() {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST["EntryControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $id = $model->id;
            $package = Package::model()->with("dependents")->findByPk($id);

            if ($package === null) {
                throw new CHttpException(404, Yii::t("app", "Package not found."));
            }

            switch ($model->operation) {
                case "delete":
                    $pm = new PackageManager();

                    if ($pm->hasDependentObjects($package)) {
                        throw new CHttpException(403, Yii::t("app", "This package is required by other objects and cannot be deleted."));
                    }

                    try {
                        SystemManager::updateStatus(
                            System::STATUS_PACKAGE_MANAGER,
                            array(System::STATUS_IDLE, System::STATUS_PACKAGE_MANAGER)
                        );
                    } catch (Exception $e) {
                        throw new CHttpException(403, Yii::t("app", "Access denied."));
                    }

                    // schedule package for deletion
                    $package->status = Package::STATUS_DELETE;
                    $package->save();

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Upload package
     * @param $type int package type
     */
    public function actionUpload() {
        $response = new AjaxResponse();

        try {
            $model = new PackageUploadForm();
            $model->file = CUploadedFile::getInstanceByName("PackageUploadForm[file]");

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $pm = new PackageManager();
            $package = $pm->upload($model);

            // "package" is a reserved javascript word, so we use "pkg" instead
            $response->addData("pkg", $package);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * View package
     * @param $id
     * @throws CHttpException
     */
    public function actionView($id) {
        $package = Package::model()->findByPk($id);

        if (!$package || !$package->isActive()) {
            throw new CHttpException(404, Yii::t("app", "Package not found."));
        }

        $pm = new PackageManager();
        $data = $pm->getData($package);

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array($package->name, "");

        // display the page
        $this->pageTitle = $package->name;
		$this->render("view", array(
            "package" => $package,
            "data" => $data
        ));
    }

    /**
     * Share package
     * @param $id
     * @throws CHttpException
     */
    public function actionShare($id) {
        $package = Package::model()->findByPk($id);

        if (!$package) {
            throw new CHttpException(404, Yii::t("app", "Package not found."));
        }

        $form = new SharePackageForm();

		if (isset($_POST["SharePackageForm"])) {
			$form->attributes = $_POST["SharePackageForm"];

			if ($form->validate()) {
                try {
                    $pm = new PackageManager();
                    $pm->prepareSharing($package);
                } catch (Exception $e) {
                    throw new CHttpException(403, Yii::t("app", "Access denied."));
                }

                Yii::app()->user->setFlash("success", Yii::t("app", "Package scheduled for sharing."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array($package->name, $this->createUrl("package/view", array("id" => $id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Share"), "");

        // display the page
        $this->pageTitle = $package->name;
		$this->render("share", array(
            "package" => $package,
        ));
    }

    /**
     * Regenerate.
     */
	public function actionRegenerate() {
        if (isset($_POST["RegenerateForm"])) {
            try {
                SystemManager::updateStatus(System::STATUS_REGENERATE_SANDBOX, System::STATUS_IDLE);
                $this->_system->refresh();
            } catch (Exception $e) {
                throw new CHttpException(403, Yii::t("app", "Access denied."));
            }
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Scripts"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Regenerate Sandbox"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Regenerate Sandbox");
		$this->render("regenerate", array(
            "system" => $this->_system
        ));
	}

    /**
     * Regenerate status page
     */
    public function actionRegenerateStatus() {
        $response = new AjaxResponse();

        try {
            $system = System::model()->findByPk(1);
            $response->addData("regenerating", $system->status == System::STATUS_REGENERATE_SANDBOX);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}
