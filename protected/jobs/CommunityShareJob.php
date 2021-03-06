<?php

/**
 * Class CommunityShareJob
 */
class CommunityShareJob extends BackgroundJob {
    const TYPE_ALL        = "all";
    const TYPE_REFERENCE  = "reference";
    const TYPE_CATEGORY   = "category";
    const TYPE_CONTROL    = "control";
    const TYPE_PACKAGE    = "package";
    const TYPE_CHECK      = "check";
    const TYPE_FIELD      = "field";

    const ID_TEMPLATE  = 'gtta.@type@.@obj_id@.share';

    /*
     * Recursive share
     */
    private $_recursive = false;

    /**
     * Share reference
     */
    private function _shareReference($id) {
        $rm = new ReferenceManager();
        $reference = Reference::model()->findByPk($id);

        if (!$reference) {
            throw new Exception("Reference not found.");
        }

        $rm->share($reference);
    }

    /**
     * Share category
     */
    private function _shareCategory($id) {
        $cm = new CategoryManager();
        $category = CheckCategory::model()->findByPk($id);

        $cm->share($category, $this->_recursive);
    }

    /**
     * Share control
     */
    private function _shareControl($id) {
        $cm = new ControlManager();
        $control = CheckControl::model()->findByPk($id);

        if (!$control) {
            throw new Exception("Control not found.");
        }

        $cm->share($control, $this->_recursive);
    }

    /**
     * Install package
     */
    private function _sharePackage($id) {
        $pm = new PackageManager();
        $package = Package::model()->with("dependencies")->findByPk($id);

        if (!$package) {
            throw new Exception("Package not found");
        }

        foreach ($package->dependencies as $dep) {
            if ($dep->external_id) {
                continue;
            }

            $pm->share($dep);
        }

        $pm->share($package);
    }

    /**
     * Share check
     */
    private function _shareCheck($id) {
        $cm = new CheckManager();
        $check = Check::model()->findByPk($id);

        if (!$check) {
            throw new Exception("Check not found");
        }

        if ($check->private) {
            throw new Exception("Check is private");
        }

        $cm->share($check);
    }

    /**
     * Share field
     * @param $id
     * @throws Exception
     */
    private function _shareField($id) {
        $fm = new FieldManager();
        $field = GlobalCheckField::model()->findByPk($id);

        if (!$field) {
            throw new Exception("Field not found");
        }

        $fm->share($field);
    }

    /**
     * Share all
     * @throws Exception
     */
    private function _shareAll() {
        $categories = CheckCategory::model()->findAll();

        foreach ($categories as $category) {
            $this->_shareCategory($category->id);
        }
    }

    /**
     * Share check preparations
     * @param $type
     * @param $id
     * @throws Exception
     */
    private function _share($type, $id) {
        try {
            switch ($type) {
                case self::TYPE_ALL:
                    $this->_shareAll();
                    break;
                case self::TYPE_REFERENCE:
                    $this->_shareReference($id);
                    break;
                case self::TYPE_CATEGORY:
                    $this->_shareCategory($id);
                    break;
                case self::TYPE_CONTROL:
                    $this->_shareControl($id);
                    break;
                case self::TYPE_PACKAGE:
                    $this->_sharePackage($id);
                    break;
                case self::TYPE_CHECK:
                    $this->_shareCheck($id);
                    break;
                case self::TYPE_FIELD:
                    $this->_shareField($id);
                    break;
                default:
                    return;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Perform
     */
    public function perform() {
        try {
            if (!isset($this->args["type"]) || ($this->args["type"] != self::TYPE_ALL && !isset($this->args["obj_id"]))) {
                throw new Exception("Invalid job params.");
            }

            $this->_recursive = isset($this->args["recursive"]) && $this->args["recursive"] || $this->args["type"] == self::TYPE_ALL;

            $type = $this->args["type"];
            $allowedTypes = [
                self::TYPE_ALL,
                self::TYPE_REFERENCE,
                self::TYPE_CATEGORY,
                self::TYPE_CONTROL,
                self::TYPE_PACKAGE,
                self::TYPE_CHECK,
                self::TYPE_FIELD,
            ];

            if (!in_array($type, $allowedTypes)) {
                throw new Exception("Invalid type.");
            }

            $id = isset($this->args["obj_id"]) ? $this->args["obj_id"] : null;

            $this->_share($type, $id);
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}