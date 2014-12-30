<?php

/**
 * Class CommunityShareJob
 */
class CommunityShareJob extends BackgroundJob {
    /**
     * System flag
     */
    const SYSTEM = false;

    const TYPE_REFERENCE  = "reference";
    const TYPE_CATEGORY   = "category";
    const TYPE_CONTROL    = "control";
    const TYPE_PACKAGE    = "package";
    const TYPE_CHECK      = "check";

    const JOB_ID   = '@app@.@type@.@obj_id@.share';

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

        $cm->share($category);
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

        $cm->share($control);
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

        $cm->share($check);
    }

    /**
     * Share check preparations
     */
    private function _share($type, $id) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        if ($system->pid !== null) {
            if (ProcessManager::isRunning($system->pid)) {
                return;
            }

            System::model()->updateByPk(1, array(
                "pid" => null,
            ));

            return;
        }

        $exception = null;

        try {
            switch ($type) {
                case $this::TYPE_REFERENCE:
                    $this->_shareReference($id);
                    break;
                case $this::TYPE_CATEGORY:
                    $this->_shareCategory($id);
                    break;
                case $this::TYPE_CONTROL:
                    $this->_shareControl($id);
                    break;
                case $this::TYPE_PACKAGE:
                    $this->_sharePackage($id);
                    break;
                case $this::TYPE_CHECK:
                    $this->_shareCheck($id);
                    break;
                default:
                    return;
            }
        } catch (Exception $e) {
            $exception = $e;
        }

        System::model()->updateByPk(1, array(
            "pid" => posix_getpgid(getmypid()),
        ));

        // "finally" block emulation
        try {
            System::model()->updateByPk(1, array(
                "pid" => null,
            ));
        } catch (Exception $e) {
            // swallow exceptions
        }

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Perform
     */
    public function perform() {
        if (!isset($this->args['type']) || !isset($this->args['obj_id'])) {
            throw new Exception('Invalid job params.');
        }

        $type = $this->args['type'];
        $allowedTypes = array(
            $this::TYPE_REFERENCE,
            $this::TYPE_CATEGORY,
            $this::TYPE_CONTROL,
            $this::TYPE_PACKAGE,
            $this::TYPE_CHECK,
        );

        if (!in_array($type, $allowedTypes)) {
            throw new Exception("Invalid type.");
        }

        $id = $this->args['obj_id'];

        $this->_share($type, $id);
    }
}