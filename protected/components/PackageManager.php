<?php

class InvalidName extends Exception {};
class InvalidLanguage extends Exception {};
class MissingSectionException extends Exception {};

/**
 * Package manager class
 */
class PackageManager {
    const DESCRIPTION_FILE = "package.yaml";
    const SECTION_NAME = "name";
    const SECTION_TYPE = "type";
    const SECTION_VERSION = "version";
    const SECTION_DESCRIPTION = "description";
    const SECTION_VALUE = "value";
    const SECTION_DEPENDENCIES = "dependencies";
    const SECTION_INPUTS = "inputs";
    const TYPE_LIBRARY = "library";
    const TYPE_SCRIPT = "script";
    const DEPENDENCY_TYPE_LIBRARY = "library";
    const DEPENDENCY_TYPE_SCRIPT = "script";
    const DEPENDENCY_TYPE_SYSTEM = "system";
    const DEPENDENCY_TYPE_DEB = "deb";
    const DEPENDENCY_TYPE_PYTHON = "python";
    const DEPENDENCY_TYPE_PERL = "perl";
    const INPUT_TYPE_TEXT = "text";
    const INPUT_TYPE_TEXTAREA = "textarea";
    const INPUT_TYPE_CHECKBOX = "checkbox";
    const INPUT_TYPE_RADIO = "radio";
    const INPUT_TYPE_FILE = "file";

    /**
     * Get entry point names
     * @return array
     */
    private function _getEntryPointNames() {
        return array(
            "run.py",
            "run.pl",
        );
    }

    /**
     * Get forbidden package names
     * @return array
     */
    private function _getForbiddenPackageNames() {
        return array(
            "lib",
        );
    }

    /**
     * Get dependency types
     * @return array
     */
    private function _getDependencyTypes() {
        return array(
            self::DEPENDENCY_TYPE_LIBRARY,
            self::DEPENDENCY_TYPE_SCRIPT,
            self::DEPENDENCY_TYPE_SYSTEM,
            self::DEPENDENCY_TYPE_DEB,
            self::DEPENDENCY_TYPE_PYTHON,
            self::DEPENDENCY_TYPE_PERL
        );
    }

    /**
     * Convert package type
     * @param $type
     * @return mixed
     * @throws Exception
     */
    private function _convertPackageType($type) {
        $types = array(
            self::TYPE_LIBRARY => Package::TYPE_LIBRARY,
            self::TYPE_SCRIPT => Package::TYPE_SCRIPT
        );

        if (!isset($types[$type])) {
            throw new Exception(Yii::t("app", "Invalid package type."));
        }

        return $types[$type];
    }

    /**
     * Get input types
     * @return array
     */
    private function _getInputTypes() {
        return array(
            self::INPUT_TYPE_TEXT,
            self::INPUT_TYPE_TEXTAREA,
            self::INPUT_TYPE_CHECKBOX,
            self::INPUT_TYPE_RADIO,
            self::INPUT_TYPE_FILE
        );
    }

    /**
     * Get type name
     * @param $type
     * @return mixed
     * @throws Exception
     */
    private function _getTypeName($type) {
        $types = array(
            Package::TYPE_LIBRARY => Yii::t("app", "Library"),
            Package::TYPE_SCRIPT => Yii::t("app", "Script"),
        );

        if (!isset($types[$type])) {
            throw new Exception(Yii::t("app", "Invalid package type."));
        }

        return $types[$type];
    }

    /**
     * Check if package has dependent objects
     * @param Package $package
     * @return boolean
     */
    public function hasDependentObjects(Package $package) {
        if (count($package->dependents) > 0) {
            return true;
        }

        // find out if it has dependent checks
        if ($package->type == Package::TYPE_SCRIPT) {
            $language = Language::model()->findByAttributes(array(
                "code" => Yii::app()->language
            ));

            if ($language) {
                $language = $language->id;
            }

            $checks = CheckScript::model()->with(array(
                "check" => array(
                    "with" => array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "language_id = :language_id",
                            "params" => array("language_id" => $language)
                        ),
                    )
                )    
            ))->findAllByAttributes(array(
                "package_id" => $package->id
            ));

            if (count($checks) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract package files
     * @param $zipPath string zip file path
     * @param $packageDir string package directory
     */
    private function _extract($zipPath, $packageDir) {
        if (!@file_exists($zipPath)) {
            throw new Exception(Yii::t("app", "File not found."));
        }

        if (is_dir($packageDir)) {
            FileManager::rmDir($packageDir);
        }

        $zip = new ZipArchive();

        if (!$zip->open($zipPath)) {
            throw new Exception(Yii::t("app", "Unable to open the package."));
        }

        if (!$zip->extractTo($packageDir)) {
            throw new Exception(Yii::t("app", "Error extracting package files."));
        }

        $zip->close();
    }

    /**
     * Get hash section
     * @param $hash
     * @param $section
     * @param bool $required
     * @return mixed
     */
    private function _getSection($hash, $section, $required=true) {
        if (!isset($hash[$section]) && $required) {
            throw new MissingSectionException($section);
        }

        if (isset($hash[$section])) {
            return $hash[$section];
        }

        return null;
    }

    /**
     * Check name
     * @param $name
     * @param bool $multiLanguage
     * @throws Exception
     */
    private function _checkName($name, $multiLanguage=false) {
        if (!$multiLanguage && (!is_string($name) || !preg_match('/^[A-Za-z0-9\-_:\.=]+$/', $name))) {
            throw new InvalidName();
        }

        if ($multiLanguage && !is_string($name)) {
            if (!is_array($name)) {
                throw new Exception(Yii::t("app", "Invalid package name."));
            }

            $languages = Language::model()->findAll();

            foreach (array_keys($name) as $code) {
                $languageFound = false;

                foreach ($languages as $language) {
                    if ($code == $language->code) {
                        $languageFound = true;
                        break;
                    }
                }

                if (!$languageFound) {
                    throw new Exception(
                        Yii::t("app", "Invalid language code: {code}.", array("{code}" => $code))
                    );
                }
            }
        }
    }

    /**
     * Get package root path
     * @param $path
     * @return string
     */
    private function _getRootPath($path) {
        $contents = FileManager::getDirectoryContents($path);

        if (count($contents) == 1 && is_dir($path . "/" . $contents[0])) {
            $path = $path . "/" . $contents[0];
        }

        return $path;
    }

    /**
     * Parse package
     * @param $path string package path
     * @return array
     * @throws Exception
     */
    private function _parse($path) {
        $description = $path . "/" . self::DESCRIPTION_FILE;
        $entryPoint = null;

        if (!@file_exists($description)) {
            throw new Exception(Yii::t("app", "Description file not found."));
        }

        $package = @yaml_parse_file($description);

        if (!$package) {
            throw new Exception(Yii::t("app", "Description file parsing error."));
        }

        $name = null;
        $version = null;
        $type = null;
        $description = null;
        $dependencies = array();
        $inputs = array();

        foreach ($this->_getDependencyTypes() as $type) {
            $dependencies[$type] = array();
        }

        try {
            $name = $this->_getSection($package, self::SECTION_NAME);
            $version = $this->_getSection($package, self::SECTION_VERSION);
            $type = $this->_getSection($package, self::SECTION_TYPE);
        } catch (MissingSectionException $e) {
            throw new Exception(
                Yii::t("app", "Missing package section:: {section}.", array(
                    "{section}" => $e->getMessage()
                ))
            );
        }

        try {
            $this->_checkName($name);
        } catch (InvalidName $e) {
            throw new Exception(
                Yii::t("app", "Invalid package name: {name}.", array("{name}" => $name))
            );
        }

        if (!preg_match('/^\d+\.\d+$/', $version)) {
            throw new Exception(
                Yii::t("app", "Invalid version: {version}.", array("{version}" => $version))
            );
        }

        $type = $this->_convertPackageType($type);
        $deps = $this->_getSection($package, self::SECTION_DEPENDENCIES, false);

        if ($deps) {
            foreach ($deps as $depType => $dependencyList) {
                if (!in_array($depType, $this->_getDependencyTypes())) {
                    throw new Exception(
                        Yii::t("app", "Invalid dependency type: {type}", array("{type}" => $depType))
                    );
                }

                if (!is_array($dependencyList)) {
                    $dependencyList = array($dependencyList);
                }

                foreach ($dependencyList as $dependency) {
                    try {
                        $this->_checkName($dependency, false);
                    } catch (InvalidName $e) {
                        throw new Exception(
                            Yii::t("app", "Invalid dependency name: {name}.", array("{name}" => $dependency))
                        );
                    }

                    $dependencies[$depType][] = $dependency;
                }
            }
        }

        if ($type == Package::TYPE_SCRIPT) {
            $inps = $this->_getSection($package, self::SECTION_INPUTS, false);

            if ($inps) {
                foreach ($inps as $input) {
                    try {
                        $inputName = $this->_getSection($input, self::SECTION_NAME);
                        $inputType = $this->_getSection($package, self::SECTION_TYPE);
                        $inputValue = $this->_getSection($package, self::SECTION_VALUE, false);
                    } catch (MissingSectionException $e) {
                        throw new Exception(
                            Yii::t("app", "Missing section {section} for input.", array(
                                "{section}" => $e->getMessage()
                            ))
                        );
                    }

                    try {
                        $this->_checkName($inputName);
                    } catch (InvalidName $e) {
                        throw new Exception(
                            Yii::t("app", "Invalid input name: {name}.", array("{name}" => $inputName))
                        );
                    }

                    if (!in_array($inputType, $this->_getInputTypes())) {
                        throw new Exception(
                            Yii::t("app", "Invalid input type: {type}", array("{type}" => $inputType))
                        );
                    }

                    $inputs[] = array(
                        self::SECTION_NAME => $inputName,
                        self::SECTION_TYPE => $inputType,
                        self::SECTION_VALUE => $inputValue
                    );
                }
            }
        }

        // scan top-level sections
        $allowedTopLevel = array(
            self::SECTION_NAME,
            self::SECTION_TYPE,
            self::SECTION_VERSION,
            self::SECTION_DESCRIPTION,
            self::SECTION_DEPENDENCIES,
            self::SECTION_INPUTS,
        );

        foreach ($package as $key => $value) {
            if (!in_array($key, $allowedTopLevel)) {
                throw new Exception(Yii::t("app", "Invalid section: {section}", array("{section}" => $key)));
            }
        }

        return array(
            self::SECTION_TYPE => $type,
            self::SECTION_NAME => $name,
            self::SECTION_VERSION => $version,
            self::SECTION_DEPENDENCIES => $dependencies,
            self::SECTION_INPUTS => $inputs,
            "path" => $path,
        );
    }

    /**
     * Check if system package dependency is installed
     * @param $package
     * @throws Exception
     */
    private function _checkSystemDependency($package) {
        $vm = new VMManager();

        try {
            $vm->runCommand("dpkg --list $package | grep ^ii");
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unsatisfied system dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Check if deb package dependency is installed
     * @param $package
     * @param $packagePath
     * @throws Exception
     */
    private function _checkDebDependency($package, $packagePath) {
        $vm = new VMManager();

        try {
            $name = explode("_", $package);
            $name = $name[0];

            $requiredVersion = ProcessManager::runCommand("dpkg --info $packagePath/$package | grep Version | cut -d \" \" -f 3");
            $currentVersion = $vm->runCommand("dpkg-query -W $name | cut -f 2");

            if ($currentVersion != $requiredVersion) {
                throw new Exception();
            }
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unsatisfied deb dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Check if python dependency is installed
     * @param $package
     * @throws Exception
     */
    private function _checkPythonDependency($package) {
        $vm = new VMManager();

        try {
            $vm->runCommand("pip freeze | grep $package");
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unsatisfied python dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Check if perl dependency is installed
     * @param $package
     * @throws Exception
     */
    private function _checkPerlDependency($package) {
        $vm = new VMManager();

        try {
            $vm->runCommand("perl -M$package -e1");
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unsatisfied perl dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Check dependencies
     * @param string $package
     * @param boolean $strict
     * @throws Exception
     */
    private function _checkDependencies($package, $strict) {
        $dependencies = $package[self::SECTION_DEPENDENCIES];

        // library dependencies
        $libraries = $this->_getSection($dependencies, self::DEPENDENCY_TYPE_LIBRARY, false);

        if ($libraries) {
            foreach ($libraries as $library) {
                $check = Package::model()->findByAttributes(array(
                    "type" => Package::TYPE_LIBRARY,
                    "status" => Package::STATUS_INSTALLED,
                    "name" => $library,
                ));

                if (!$check) {
                    throw new Exception(
                        Yii::t("app", "Unsatisfied library dependency: {library}.", array("{library}" => $library))
                    );
                }
            }
        }

        // script dependencies
        $scripts = $this->_getSection($dependencies, self::DEPENDENCY_TYPE_SCRIPT, false);

        if ($scripts) {
            foreach ($scripts as $script) {
                $check = Package::model()->findByAttributes(array(
                    "type" => Package::TYPE_SCRIPT,
                    "status" => Package::STATUS_INSTALLED,
                    "name" => $script,
                ));

                if (!$check) {
                    throw new Exception(
                        Yii::t("app", "Unsatisfied script dependency: {script}.", array("{script}" => $script))
                    );
                }
            }
        }

        if ($strict) {
            // system dependencies
            $packages = $this->_getSection($dependencies, self::DEPENDENCY_TYPE_SYSTEM, false);

            if ($packages) {
                foreach ($packages as $pkg) {
                    $this->_checkSystemDependency($pkg);
                }
            }

            // deb dependencies
            $packages = $this->_getSection($dependencies, self::DEPENDENCY_TYPE_DEB, false);

            if ($packages) {
                foreach ($packages as $pkg) {
                    $this->_checkDebDependency($pkg, $package["path"]);
                }
            }

            // python dependencies
            $packages = $this->_getSection($dependencies, self::DEPENDENCY_TYPE_PYTHON, false);

            if ($packages) {
                foreach ($packages as $pkg) {
                    $this->_checkPythonDependency($pkg);
                }
            }

            // perl dependencies
            $packages = $this->_getSection($dependencies, self::DEPENDENCY_TYPE_PERL, false);

            if ($packages) {
                foreach ($packages as $pkg) {
                    $this->_checkPerlDependency($pkg);
                }
            }
        }
    }

    /**
     * Validate package by path
     * @param $package mixed package
     * @param $strict boolean strict dependency validation
     * @return array
     * @throws Exception
     */
    private function _validate($package, $strict=true) {
        if (is_object($package) && $package instanceof Package) {
            $package = $this->_parse($this->getPath($package));
        }

        $entryPoint = null;

        if (in_array($package[self::SECTION_NAME], $this->_getForbiddenPackageNames())) {
            throw new Exception(Yii::t("app", "Invalid package name."));
        }

        if ($package[self::SECTION_TYPE] == Package::TYPE_SCRIPT) {
            foreach ($this->_getEntryPointNames() as $ep) {
                if (file_exists($package["path"] . "/" . $ep)) {
                    $entryPoint = $package["path"] . "/" . $ep;
                    break;
                }
            }

            if ($entryPoint === null) {
                throw new Exception(Yii::t("app", "Entry point not found."));
            }
        }

        $this->_checkDependencies($package, $strict);

        return $package;
    }

    /**
     * Schedule package for installation
     * @param $id
     * @return Package
     * @throws Exception
     */
    public function scheduleForInstallation($id) {
        $packagePath = Yii::app()->params["packages"]["tmpPath"] . "/" . $id;
        $zipPath = Yii::app()->params["packages"]["tmpPath"] . "/" . $id . ".zip";
        $package = null;
        $exception = null;
        $pkg = null;

        try {
            $this->_extract($zipPath, $packagePath);
            $package = $this->_parse($this->_getRootPath($packagePath));
            $this->_validate($package, false);

            $pkg = Package::model()->findByAttributes(array(
                "type" => $package[self::SECTION_TYPE],
                "name" => $package[self::SECTION_NAME]
            ));

            if (!$pkg) {
                $pkg = new Package();
            }

            $pkg->file_name = $id;
            $pkg->name = $package[self::SECTION_NAME];
            $pkg->type = $package[self::SECTION_TYPE];
            $pkg->version = $package[self::SECTION_VERSION];
            $pkg->status = Package::STATUS_INSTALL;
            $pkg->save();
        } catch (Exception $e) {
            FileManager::unlink($zipPath);
            $exception = $e;
        }

        FileManager::rmDir($packagePath);

        if ($exception) {
            throw $exception;
        }

        return $pkg;
    }

    /**
     * Get temporary package path
     * @return array
     */
    private function _getTemporaryPackageData() {
        $packageDir = Yii::app()->params["packages"]["tmpPath"];

        if (!is_dir($packageDir)) {
            FileManager::createDir($packageDir, 0777);
        }

        $id = md5(uniqid("", true));

        return array(
            "id" => $id,
            "packagePath" => $packageDir . "/" . $id,
            "zipPath" => $packageDir . "/" . $id . ".zip"
        );
    }

    /**
     * Analyze uploaded package
     * @param PackageUploadForm $model
     * @throws Exception
     * @return array
     */
    public function upload($model) {
        $tmpData = $this->_getTemporaryPackageData();
        $packagePath = $tmpData["packagePath"];
        $zipPath = $tmpData["zipPath"];
        $model->file->saveAs($zipPath);

        $exception = null;
        $package = null;

        try {
            $this->_extract($zipPath, $packagePath);
            $package = $this->_parse($this->_getRootPath($packagePath));
            $this->_validate($package, false);

            if ($package[self::SECTION_TYPE] != $model->type) {
                throw new Exception(Yii::t("app", "Invalid package type."));
            }
        } catch (Exception $e) {
            FileManager::unlink($zipPath);
            $exception = $e;
        }

        FileManager::rmDir($packagePath);

        if ($exception) {
            throw $exception;
        }

        $package["id"] = $tmpData["id"];
        $package["type"] = $this->_getTypeName($package["type"]);

        return $package;
    }

    /**
     * Install system dependency through the apt system
     * @param $package
     * @throws Exception
     */
    private function _installSystemDependency($package) {
        $vm = new VMManager();

        try {
            $vm->runCommand("DEBIAN_FRONTEND=noninteractive apt-get -y --no-install-recommends install $package");
            $this->_checkSystemDependency($package);
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unable to install system dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Install deb dependency
     * @param $package
     * @throws Exception
     */
    private function _installDebDependency($package, $packagePath) {
        $vm = new VMManager();
        $debPath = "$packagePath/$package";
        $virtualDebPath = $vm->virtualizePath("/tmp/$package");
        $exception = null;

        try {
            FileManager::copy($debPath, $virtualDebPath);
            $vm->runCommand("dpkg --install $virtualDebPath");
            $this->_checkDebDependency($package, $packagePath);
        } catch (Exception $e) {
            $exception = new Exception(
                Yii::t("app", "Unable to install deb dependency: {dependency}.", array("{dependency}" => $package))
            );
        }

        FileManager::unlink($virtualDebPath);

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Install python dependency through the pip system
     * @param $package
     * @throws Exception
     */
    private function _installPythonDependency($package) {
        $vm = new VMManager();

        try {
            $vm->runCommand("pip install $package");
            $this->_checkPythonDependency($package);
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unable to install python dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Install perl dependency through the cpan system
     * @param $package
     * @throws Exception
     */
    private function _installPerlDependency($package) {
        $vm = new VMManager();

        try {
            $vm->runCommand("cpan -D $package && PERL_MM_USE_DEFAULT=1 perl -MCPAN -e 'install $package'");
            $this->_checkPerlDependency($package);
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unable to install perl dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Install dependencies
     * @param $package mixed parsed package
     */
    private function _installDependencies($package) {
        if (is_object($package) && $package instanceof Package) {
            $package = $this->_parse($this->getPath($package));
        }

        $dependencies = $this->_getSection($package, self::SECTION_DEPENDENCIES, false);

        if (!$dependencies) {
            return;
        }

        foreach ($dependencies[self::DEPENDENCY_TYPE_SYSTEM] as $dependency) {
            try {
                $this->_checkSystemDependency($dependency);
            } catch (Exception $e) {
                $this->_installSystemDependency($dependency);
            }
        }

        foreach ($dependencies[self::DEPENDENCY_TYPE_DEB] as $dependency) {
            try {
                $this->_checkDebDependency($dependency, $package["path"]);
            } catch (Exception $e) {
                $this->_installDebDependency($dependency, $package["path"]);
            }
        }

        foreach ($dependencies[self::DEPENDENCY_TYPE_PYTHON] as $dependency) {
            try {
                $this->_checkPythonDependency($dependency);
            } catch (Exception $e) {
                $this->_installPythonDependency($dependency);
            }
        }

        foreach ($dependencies[self::DEPENDENCY_TYPE_PERL] as $dependency) {
            try {
                $this->_checkPerlDependency($dependency);
            } catch (Exception $e) {
                $this->_installPerlDependency($dependency);
            }
        }
    }

    /**
     * Install package
     * @param Package $package
     * @throws Exception
     */
    public function install(Package $package) {
        $packageDir = Yii::app()->params["packages"]["tmpPath"];
        $packagePath = $packageDir . "/" . $package->file_name;
        $zipPath = $packageDir . "/" . $package->file_name . ".zip";
        $exception = null;
        $destinationPath = null;
        $vm = new VMManager();

        try {
            $this->_extract($zipPath, $packagePath);
            $parsedPackage = $this->_parse($this->_getRootPath($packagePath));
            $this->_validate($parsedPackage, false);

            $destinationPath = $this->getPath($package);
            FileManager::rmDir($destinationPath);
            FileManager::createDir($destinationPath, 0755);
            FileManager::chown($destinationPath, "gtta", "gtta");
            FileManager::copyRecursive($this->_getRootPath($packagePath), $destinationPath);

            // change "files" directory permission
            try {
                $filesPath = "$destinationPath/files";

                if (!@is_dir($filesPath)) {
                    FileManager::createDir($filesPath, 0775);
                }

                FileManager::chmod($filesPath, 0775);
                FileManager::chown($filesPath, "gtta", "gtta");
            } catch (Exception $e) {
                // pass
            }

            // install dependencies
            $dependencies = $this->_getSection($parsedPackage, self::SECTION_DEPENDENCIES, false);
            $this->_installDependencies($parsedPackage);

            // validate again
            $this->_validate($parsedPackage, true);

            // copy check to VM
            FileManager::createDir($vm->virtualizePath($destinationPath), 0755);
            FileManager::copyRecursive($destinationPath, $vm->virtualizePath($destinationPath));

            // create library dependencies
            foreach ($dependencies[self::DEPENDENCY_TYPE_LIBRARY] as $dependency) {
                $library = Package::model()->findByAttributes(array(
                    "type" => Package::TYPE_LIBRARY,
                    "name" => $dependency
                ));

                if (!$library) {
                    throw new Exception(
                        Yii::t("app", "Unsatisfied library dependency: {library}.", array("{library}" => $dependency))
                    );
                }

                $packageDep = new PackageDependency();
                $packageDep->from_package_id = $package->id;
                $packageDep->to_package_id = $library->id;
                $packageDep->save();
            }

            // create script dependencies
            foreach ($dependencies[self::DEPENDENCY_TYPE_SCRIPT] as $dependency) {
                $script = Package::model()->findByAttributes(array(
                    "type" => Package::TYPE_SCRIPT,
                    "name" => $dependency
                ));

                if (!$script) {
                    throw new Exception(
                        Yii::t("app", "Unsatisfied script dependency: {script}.", array("{script}" => $dependency))
                    );
                }

                $packageDep = new PackageDependency();
                $packageDep->from_package_id = $package->id;
                $packageDep->to_package_id = $script->id;
                $packageDep->save();
            }

            $package->file_name = null;
            $package->status = Package::STATUS_INSTALLED;
            $package->save();
        } catch (Exception $e) {
            $exception = $e;

            FileManager::rmDir($destinationPath);
            FileManager::rmDir($vm->virtualizePath($destinationPath));

            PackageDependency::model()->deleteAllByAttributes(array(
                "from_package_id" => $package->id
            ));
        }

        FileManager::unlink($zipPath);
        FileManager::rmDir($packagePath);

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Delete package
     * @param Package $package
     * @throws Exception
     */
    public function delete(Package $package) {
        if ($this->hasDependentObjects($package)) {
            throw new Exception(Yii::t("app", "This package is required by other objects and cannot be deleted."));
        }

        FileManager::rmDir($this->getPath($package));

        PackageDependency::model()->deleteAllByAttributes(array(
            "from_package_id" => $package->id
        ));

        $package->delete();
    }

    /**
     * Get installed package data
     * @param Package $package
     * @return array
     */
    public function getData(Package $package) {
        $path = $this->_getRootPath($this->getPath($package));
        $packageData = $this->_parse($path);
        $packageData["type"] = $this->_getTypeName($packageData["type"]);

        $libraries = array();
        $scripts = array();

        foreach ($packageData["dependencies"]["library"] as $library) {
            $package = Package::model()->findByAttributes(array(
                "type" => Package::TYPE_LIBRARY,
                "name" => $library
            ));

            $libraries[$package->id] = $package->name;
        }

        foreach ($packageData["dependencies"]["script"] as $script) {
            $package = Package::model()->findByAttributes(array(
                "type" => Package::TYPE_SCRIPT,
                "name" => $script
            ));

            $scripts[$package->id] = $package->name;
        }

        $packageData["dependencies"]["library"] = $libraries;
        $packageData["dependencies"]["script"] = $scripts;

        return $packageData;
    }

    /**
     * Get path
     * @param Package $package
     * @return string path
     */
    public function getPath(Package $package) {
        $paths = Yii::app()->params["packages"]["path"];
        $path = null;

        if ($package->type == Package::TYPE_LIBRARY) {
            $path = $paths["libraries"];
        } else {
            $path = $paths["scripts"];
        }

        return $path . "/" . $package->name;
    }

    /**
     * Get files path
     * @param Package $package
     * @return string path
     */
    public function getFilesPath(Package $package) {
        return $this->getPath($package) . "/files";
    }

    /**
     * Get package entry point
     * @param Package $package
     * @return string entry point
     * @throws Exception
     */
    public function getEntryPoint(Package $package) {
        $entryPoint = null;
        $path = $this->getPath($package);

        foreach ($this->_getEntryPointNames() as $ep) {
            if (file_exists($path . "/" . $ep)) {
                $entryPoint = $ep;
                break;
            }
        }

        if ($entryPoint === null) {
            throw new Exception(Yii::t("app", "Entry point not found."));
        }

        return $entryPoint;
    }

    /**
     * Get package interpreter
     * @param Package $package
     * @return array interpreter
     */
    public function getInterpreter(Package $package) {
        $interpreters = Yii::app()->params["automation"]["interpreters"];
        $entryPoint = $this->getEntryPoint($package);
        $extension = pathinfo($entryPoint, PATHINFO_EXTENSION);
        $interpreter = null;

        if (isset($interpreters[$extension])) {
            $interpreter = $interpreters[$extension];
        }

        if (!$interpreter || !file_exists($interpreter["path"])) {
            throw new Exception(Yii::t("app", "Interpreter not found."));
        }

        return $interpreter;
    }

    /**
     * Parse and install all package dependencies
     */
    public function installAllDependencies() {
        // core package goes first
        $core = Package::model()->findByAttributes(array(
            "name" => "core",
            "type" => Package::TYPE_LIBRARY
        ));

        if (!$core) {
            return;
        }

        $this->_installDependencies($core);
        $this->_validate($core, true);

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("status" => Package::STATUS_INSTALLED));
        $criteria->addNotInCondition("id", array($core->id));
        $criteria->order = "type ASC, name ASC";
        $packages = Package::model()->findAll($criteria);

        foreach ($packages as $package) {
            $this->_installDependencies($package);
            $this->_validate($package, true);
        }
    }

    /**
     * Create package by id
     * @param $package
     * @return Package
     * @throws Exception
     */
    public function create($package) {
        /** @var System $system */
        $system = System::model()->findByPk(1);
        $api = new CommunityApiClient($system->integration_key);
        $package = $api->getPackage($package)->package;

        if ($package->status == CommunityApiClient::STATUS_UNVERIFIED && !$system->community_allow_unverified) {
            throw new Exception("Installing unverified packages is prohibited.");
        }

        if ($system->community_min_rating > 0 && $package->rating < $system->community_min_rating) {
            throw new Exception("Package rating is below the system rating limit.");
        }

        $id = $package->id;
        $checkPackage = Package::model()->findByAttributes(array("external_id" => $id));

        if ($checkPackage) {
            return $checkPackage;
        }

        foreach ($package->dependencies as $dep) {
            $checkDependency = Package::model()->findByAttributes(array(
                "external_id" => $dep->id,
            ));

            if ($checkDependency) {
                continue;
            }

            $this->create($dep->id);
        }

        $tmpData = $this->_getTemporaryPackageData();
        $zipPath = $tmpData["zipPath"];

        $api = new CommunityApiClient($system->integration_key);
        $api->getPackageArchive($id, $zipPath);

        if (!file_exists($zipPath)) {
            throw new Exception("Error downloading package file: $zipPath");
        }

        $pkg = $this->scheduleForInstallation($tmpData["id"]);
        $pkg->external_id = $id;
        $pkg->save();
        $this->install($pkg);

        return $pkg;
    }

    /**
     * Get external package ids
     * @return array
     */
    public function getExternalIds() {
        $packageIds = array();
        $packages = Package::model()->findAll("external_id IS NOT NULL AND status = :status", array(
            "status" => Package::STATUS_INSTALLED
        ));

        foreach ($packages as $package) {
            $packageIds[] = $package->external_id;
        }

        return $packageIds;
    }

    /**
     * Prepare package for sharing
     * @param Package $package
     * @throws Exception
     */
    public function prepareSharing(Package $package) {
        if (!$package->isActive()) {
            throw new Exception("Invalid package.");
        }

        foreach ($package->dependencies as $dep) {
            $this->prepareSharing($dep);
        }

        if (!$package->external_id) {
            $package->status = Package::STATUS_SHARE;
            $package->save();
        }
    }

    /**
     * Share package
     * @param Package $package
     * @throws Exception
     */
    public function share(Package $package) {
        $path = $this->getPath($package);
        $zip = new ZipArchive();
        $zipPath = "/tmp/package-" . $package->id . ".zip";

        if (file_exists($zipPath)) {
            FileManager::unlink($zipPath);
        }

        if (!$zip->open($zipPath, ZipArchive::CREATE)) {
            throw new Exception(Yii::t("app", "Unable to create the package."));
        }

        FileManager::zipDirectory($zip, $path, $package->name);
        $zip->close();

        /** @var System $system */
        $system = System::model()->findByPk(1);

        try {
            $api = new CommunityApiClient($system->integration_key);
            $package->external_id = $api->sharePackage($zipPath)->id;
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
        }

        $package->status = Check::STATUS_INSTALLED;
        $package->save();

        FileManager::unlink($zipPath);
    }
}
