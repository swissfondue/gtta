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
    const SECTION_SYSTEM = "system";
    const SECTION_VALUE = "value";
    const SECTION_DEPENDENCIES = "dependencies";
    const SECTION_INPUTS = "inputs";
    const TYPE_LIBRARY = "library";
    const TYPE_SCRIPT = "script";
    const DEPENDENCY_TYPE_LIBRARY = "library";
    const DEPENDENCY_TYPE_SCRIPT = "script";
    const DEPENDENCY_TYPE_SYSTEM = "system";
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
            "run.rb"
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
                "name" => $package->name
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
        if (!$multiLanguage && (!is_string($name) || !preg_match('/^[A-Za-z0-9\-_:]+$/', $name))) {
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
        $dependencies = array();
        $inputs = array();

        foreach ($this->_getDependencyTypes() as $type) {
            $dependencies[$type] = array();
        }

        try {
            $name = $this->_getSection($package, self::SECTION_NAME);
            $version = $this->_getSection($package, self::SECTION_VERSION);
            $type = $this->_getSection($package, self::SECTION_TYPE);
            $system = $this->_getSection($package, self::SECTION_SYSTEM);
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

        if (!is_bool($system)) {
            throw new Exception(Yii::t("app", "Unexpected package system value."));
        }

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

        return array(
            self::SECTION_TYPE => $type,
            self::SECTION_NAME => $name,
            self::SECTION_VERSION => $version,
            self::SECTION_SYSTEM => $system,
            self::SECTION_DEPENDENCIES => $dependencies,
            self::SECTION_INPUTS => $inputs,
        );
    }

    /**
     * Check if system package dependency is installed
     * @param $package
     * @throws Exception
     */
    private function _checkSystemDependency($package) {
        try {
            ProcessManager::runCommand("dpkg --list $package");
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unsatisfied system dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Check if python dependency is installed
     * @param $package
     * @throws Exception
     */
    private function _checkPythonDependency($package) {
        try {
            ProcessManager::runCommand("pip freeze | grep $package==");
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
        try {
            ProcessManager::runCommand("perl -M$package -e1");
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
     * @param $packagePath string package path
     * @param $strict boolean strict dependency validation
     * @param $allowSystem boolean
     * @param $allowSameName boolean
     * @return array
     * @throws Exception
     */
    private function _validate($packagePath, $strict=true, $allowSystem=false, $allowSameName=false) {
        $packagePath = $this->_getRootPath($packagePath);
        $package = $this->_parse($packagePath);
        $entryPoint = null;

        if (!$allowSameName) {
            $pkg = Package::model()->findByAttributes(array(
                "type" => $package[self::SECTION_TYPE],
                "name" => $package[self::SECTION_NAME]
            ));

            if ($pkg) {
                throw new Exception(Yii::t("app", "Package with the same name already exists."));
            }
        }

        if (!$allowSystem && $package[self::SECTION_SYSTEM]) {
            throw new Exception(Yii::t("app", "System packages are not allowed."));
        }

        if ($package[self::SECTION_TYPE] == Package::TYPE_SCRIPT) {
            foreach ($this->_getEntryPointNames() as $ep) {
                if (file_exists($packagePath . "/" . $ep)) {
                    $entryPoint = $packagePath . "/" . $ep;
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
     * @throws Exception
     */
    public function scheduleForInstallation($id) {
        $packagePath = Yii::app()->params["packages"]["tmpPath"] . "/" . $id;
        $zipPath = Yii::app()->params["packages"]["tmpPath"] . "/" . $id . ".zip";
        $package = null;
        $exception = null;

        try {
            $this->_extract($zipPath, $packagePath);
            $package = $this->_validate($packagePath, false);

            $pkg = new Package();
            $pkg->file_name = $id;
            $pkg->name = $package[self::SECTION_NAME];
            $pkg->type = $package[self::SECTION_TYPE];
            $pkg->system = $package[self::SECTION_SYSTEM];
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
    }

    /**
     * Analyze uploaded package
     * @param PackageUploadForm $model
     * @throws Exception
     * @return array
     */
    public function upload($model) {
        $packageDir = Yii::app()->params["packages"]["tmpPath"];

        if (!is_dir($packageDir)) {
            FileManager::createDir($packageDir, 0777);
        }

        $id = md5(uniqid("", true));
        $packagePath = $packageDir . "/" . $id;
        $zipPath = $packageDir . "/" . $id . ".zip";
        $model->file->saveAs($zipPath);

        $exception = null;
        $package = null;

        try {
            $this->_extract($zipPath, $packagePath);
            $package = $this->_validate($packagePath, false);

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

        $package["id"] = $id;
        $package["type"] = $this->_getTypeName($package["type"]);

        return $package;
    }

    /**
     * Install system dependency through the apt system
     * @param $package
     * @throws Exception
     */
    private function _installSystemDependency($package) {
        try {
            ProcessManager::runCommand("apt-get -y install $package");
            $this->_checkSystemDependency($package);
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unable to install system dependency: {dependency}.", array("{dependency}" => $package))
            );
        }
    }

    /**
     * Install python dependency through the pip system
     * @param $package
     * @throws Exception
     */
    private function _installPythonDependency($package) {
        try {
            ProcessManager::runCommand("pip install $package");
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
        try {
            ProcessManager::runCommand("cpan -D $package && PERL_MM_USE_DEFAULT=1 perl -MCPAN -e 'install $package'");
            $this->_checkPerlDependency($package);
        } catch (Exception $e) {
            throw new Exception(
                Yii::t("app", "Unable to install perl dependency: {dependency}.", array("{dependency}" => $package))
            );
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

        try {
            $this->_extract($zipPath, $packagePath);
            $parsedPackage = $this->_validate($packagePath, false, false, true);
            $destinationPath = Yii::app()->params["packages"]["path"];

            if ($parsedPackage["system"]) {
                $destinationPath = $destinationPath["system"];
            } else {
                $destinationPath = $destinationPath["user"];
            }

            if ($parsedPackage["type"] == Package::TYPE_LIBRARY) {
                $destinationPath = $destinationPath["libraries"];
            } else {
                $destinationPath = $destinationPath["scripts"];
            }

            $destinationPath = $destinationPath . "/" . $parsedPackage["name"];

            FileManager::rmDir($destinationPath);
            FileManager::createDir($destinationPath, 0755);
            FileManager::copyRecursive($this->_getRootPath($packagePath), $destinationPath);

            // install dependencies
            $dependencies = $parsedPackage[self::SECTION_DEPENDENCIES];

            foreach ($dependencies[self::DEPENDENCY_TYPE_SYSTEM] as $dependency) {
                try {
                    $this->_checkSystemDependency($dependency);
                } catch (Exception $e) {
                    $this->_installSystemDependency($dependency);
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

            // validate again
            $this->_validate($destinationPath, false, false, true);

            // install library dependencies
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

            // install script dependencies
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

        if ($package->system) {
            throw new Exception(Yii::t("app", "System packages cannot be deleted."));
        }

        FileManager::rmDir($package->getPath());

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
        $path = $this->_getRootPath($package->getPath());
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
}
