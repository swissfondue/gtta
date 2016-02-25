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
            "ajaxOnly + control, messages",
            "postOnly + control, upload",
            "idle - index, regenerate, libraries, view, regeneratestatus",
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
            Package::STATUS_NOT_INSTALLED,
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
     * Display messages of scheduled packages
     */
    public function actionMessages() {
        $response = new AjaxResponse();

        $pm = new PackageManager();
        $iMes = $pm->installationMessages();
        $messages = array();

        foreach ($iMes as $message) {
            $package = Package::model()->findByPk($message['id']);

            if (!$package) {
                return;
            }

            switch ($package->status) {
                case Package::STATUS_INSTALLED:
                    $messages[] = array(
                        "status" => Package::STATUS_INSTALLED,
                        "value" => Yii::t("app", "Package {package} was installed successfully.", array("{package}" => $package->name))
                    );
                    break;
                case Package::STATUS_ERROR:
                    $messages[] = array(
                        "status" => Package::STATUS_ERROR,
                        "value" => Yii::t("app", "Package {package} was not installed. {error}", array("{package}" => $package->name, "{error}" => $message['message']))
                    );
                    break;
                default:
                    break;
            }
        }

        $response->addData("messages", $messages);

        echo $response->serialize();
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
                    $pm = new PackageManager();
                    $pm->scheduleForInstallation($model->id);
                    Yii::app()->user->setFlash("success", Yii::t("app", "Package scheduled for installation."));
                } catch (Exception $e) {
                    Yii::app()->user->setFlash("error", Yii::t("app", $e->getMessage()));
                }

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

                    // schedule package for deletion
                    PackageJob::enqueue(array(
                        "operation" => PackageJob::OPERATION_DELETE,
                        "obj_id" => $package->id,
                    ));

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

        try {
            $data = $pm->getData($package);
        } catch (Exception $e) {
            throw new CHttpException(404, Yii::t("app", "Package not found."));
        }

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
     * Edit package files
     * @param $id
     * @throws CHttpException
     */
    public function actionEditFiles($id) {
        $id = (int) $id;
        $package = Package::model()->findByPk($id);

        if (!$package) {
            throw new CHttpException(404, "Package not found.");
        }

        $selected = null;
        $files = array();
        $pm = new PackageManager();
        $form = new PackageEditFilesForm();

        try {
            if (isset($_POST['PackageEditFilesForm'])) {
                $form->attributes = $_POST['PackageEditFilesForm'];

                if ($form->validate()) {
                    $path = $pm->getPath($package) . DIRECTORY_SEPARATOR . $form->path;
                    $content = $form->content;

                    switch ($form->operation) {
                        case PackageEditFilesForm::OPERATION_SAVE:
                            FileManager::updateFile($path, $content);
                            $selected = $form->path;
                            $package->save();

                            ModifiedPackagesJob::enqueue(array(
                                "obj_id" => $package->id
                            ));

                            Yii::app()->user->setFlash("success", Yii::t("app", "File successfully saved."));

                            break;

                        case PackageEditFilesForm::OPERATION_DELETE:
                            FileManager::unlink($path);
                            $form = new PackageEditFilesForm();
                            $package->save();

                            ModifiedPackagesJob::enqueue(array(
                                "obj_id" => $package->id
                            ));

                            Yii::app()->user->setFlash("success", Yii::t("app", "File successfully deleted."));

                            break;

                        default:
                            break;
                    }
                } else {
                    Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
                }
            }

            $files = FileManager::getDirectoryContents($pm->getPath($package), true);
        } catch (Exception $e) {
            throw $e;
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array($package->name, $this->createUrl("package/view", array("id" => $package->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Edit"), "");
        $this->pageTitle = $package->name;

        $this->render("edit-files", array(
            "files" => $files,
            "selected" => $selected,
            "package" => $package,
            "form" => $form
        ));
    }

    /**
     * Edit package properties
     * @param $id
     * @throws Exception
     */
    public function actionEditProperties($id) {
        $id = (int) $id;

        $package = Package::model()->findByPk($id);

        if (!$package) {
            throw new Exception("Package not found.");
        }

        $form = new PackageEditPropertiesForm();

        if (isset($_POST["PackageEditPropertiesForm"])) {
            $form->attributes = $_POST["PackageEditPropertiesForm"];

            if ($form->validate()) {
                $package->timeout = $form->timeout;
                $package->save();

                Yii::app()->user->setFlash("success", Yii::t("app", "Package saved."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array($package->name, $this->createUrl("package/view", array("id" => $package->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Edit"), "");
        $this->pageTitle = $package->name;

        // display the page
        $this->render("edit-properties", array(
            "form" => $form,
            "package" => $package,
        ));
    }

    /**
     * Get package file content
     * @param $id
     */
    public function actionFile($id) {
        $id = (int) $id;
        $package = Package::model()->findByPk($id);

        if (!$package) {
            throw new CHttpException(404, Yii::t("app", "Package not found."));
        }

        $response = new AjaxResponse();

        try {
            $model = new PackageLoadFileForm();
            $model->attributes = $_POST['PackageLoadFileForm'];

            if (!$model->validate()) {
                $errorText = '';

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $pm = new PackageManager();

            $content = FileManager::getFileContent($pm->getPath($package) . DIRECTORY_SEPARATOR . $model->path);
            $response->addData('file_content', $content);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
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
            if (!$this->_system->isRegenerating) {
                RegenerateJob::enqueue();
            }
        }

        $this->_system->refresh();

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Regenerate Sandbox"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Regenerate Sandbox");
		$this->render("regenerate", array(
            "system" => $this->_system
        ));
	}

    /**
     * Get regenerate status
     */
    public function actionRegenerateStatus() {
        $response = new AjaxResponse();

        try {
            $system = System::model()->findByPk(1);
            $response->addData("regenerating", $system->isRegenerating);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Sync options page
     */
    public function actionSync() {
        $form = new SyncForm();

        $job = JobManager::buildId(GitJob::ID_TEMPLATE);
        $running = JobManager::isRunning($job);

        if (isset($_POST["SyncForm"])) {
            $form->attributes = $_POST["SyncForm"];

            if ($form->validate()) {
                if (!$running) {
                    GitJob::enqueue(array(
                        "strategy" => $form->strategy,
                        "email" => Yii::app()->user->email
                    ));

                    $running = true;
                }
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->_system->refresh();

        $this->breadcrumbs[] = array(Yii::t("app", "Packages"), $this->createUrl("package/index"));
        $this->breadcrumbs[] = array(Yii::t("app", "Sync"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Sync Packages");
        $this->render("sync", array(
            "sync" => $running,
            "system" => $this->_system
        ));
    }

    /**
     * Get packages sync status
     */
    public function actionSyncStatus() {
        $response = new AjaxResponse();

        try {
            $job = JobManager::buildId(GitJob::ID_TEMPLATE);
            $running = JobManager::isRunning($job);

            $response->addData("sync", $running);

            if (!$running) {
                $error = Resque::redis()->get("gtta.packages.result.sync");

                if ($error) {
                    $response->addData("error", true);
                    Resque::redis()->del("gtta.packages.result.sync");
                }
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}
