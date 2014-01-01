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
     * Display a list of scripts.
     */
	public function actionIndex($page=1) {
        $page = (int) $page;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->params["entriesPerPage"];
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];
        $criteria->addColumnCondition(array("type" => Package::TYPE_SCRIPT));
        $criteria->addInCondition("status", array(
            Package::STATUS_INSTALL,
            Package::STATUS_INSTALLED,
            Package::STATUS_ERROR
        ));

        $criteria->order = "t.system DESC, t.name ASC";
        $scripts = Package::model()->findAll($criteria);

        $scriptCount = Package::model()->count($criteria);
        $paginator = new Paginator($scriptCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Scripts"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Scripts");
		$this->render("index", array(
            "scripts" => $scripts,
            "p" => $paginator,
            "system" => $this->_system,
        ));
	}

    /**
     * Display a list of libraries.
     */
    public function actionLibraries($page=1) {
        $page = (int) $page;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->params["entriesPerPage"];
        $criteria->offset = ($page - 1) * Yii::app()->params["entriesPerPage"];
        $criteria->addColumnCondition(array("type" => Package::TYPE_LIBRARY));
        $criteria->addInCondition("status", array(
            Package::STATUS_INSTALL,
            Package::STATUS_INSTALLED,
            Package::STATUS_ERROR
        ));

        $criteria->order = "t.system DESC, t.name ASC";
        $libraries = Package::model()->findAll($criteria);

        $libCount = Package::model()->count($criteria);
        $paginator = new Paginator($libCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Libraries"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Libraries");
		$this->render("library/index", array(
            "libraries" => $libraries,
            "p" => $paginator,
            "system" => $this->_system,
        ));
    }

    /**
     * Edit package
     * @param $type int package
     */
	private function _editPackage($type) {
		$model = new PackageEditForm();

		if (isset($_POST["PackageEditForm"])) {
			$model->attributes = $_POST["PackageEditForm"];

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

                if ($type == Package::TYPE_SCRIPT) {
                    $this->redirect(array("package/index"));
                } elseif ($type == Package::TYPE_LIBRARY) {
                    $this->redirect(array("package/libraries"));
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        // display the page
		$this->render("edit", array(
            "model" => $model,
            "type" => $type,
        ));
	}

    /**
     * Edit script
     */
    public function actionEditScript() {
        $this->breadcrumbs[] = array(Yii::t("app", "Scripts"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "New Script"), "");
        $this->pageTitle = Yii::t("app", "New Script");

        $this->_editPackage(Package::TYPE_SCRIPT);
    }

    /**
     * Edit library
     */
    public function actionEditLibrary() {
        $this->breadcrumbs[] = array(Yii::t("app", "Libraries"), $this->createUrl("package/libraries"));
        $this->breadcrumbs[] = array(Yii::t("app", "New Library"), "");
        $this->pageTitle = Yii::t("app", "New Library");

        $this->_editPackage(Package::TYPE_LIBRARY);
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
                    if ($package->system) {
                        throw new CHttpException(403, Yii::t("app", "System packages cannot be deleted."));
                    }

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
            $model->attributes = $_POST["PackageUploadForm"];
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
     */
    public function actionView($id) {
        $package = Package::model()->findByPk($id);

        if (!$package || $package->status != Package::STATUS_INSTALLED) {
            throw new CHttpException(404, Yii::t("app", "Package not found."));
        }

        $pm = new PackageManager();
        $data = $pm->getData($package);

        if ($package->type == Package::TYPE_LIBRARY) {
            $this->breadcrumbs[] = array(Yii::t("app", "Libraries"), $this->createUrl("package/libraries"));
        } else {
            $this->breadcrumbs[] = array(Yii::t("app", "Scripts"), $this->createUrl("package/index"));
        }

        $this->breadcrumbs[] = array($package->name, "");

        // display the page
        $this->pageTitle = $package->name;
		$this->render("view", array(
            "package" => $package,
            "data" => $data
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
