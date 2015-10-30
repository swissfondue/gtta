<?php

/**
 * Class SystemManager
 */
class SystemManager {
    /**
     * Update system status (atomic)
     * @param $newStatus
     * @param $allowedStatuses
     * @throws Exception
     */
    public static function updateStatus($newStatus, $allowedStatuses=null) {
        $error = false;

        if ($allowedStatuses !== null && !is_array($allowedStatuses)) {
            $allowedStatuses = array($allowedStatuses);
        }

        $fp = fopen(Yii::app()->params["systemStatusLock"], "w");

        if (flock($fp, LOCK_EX)) {
            $system = System::model()->findByPk(1);

            if ($allowedStatuses !== null && !in_array($system->status, $allowedStatuses)) {
                $error = true;
            }

            if (!$error) {
                System::model()->updateByPk(1, array(
                    "status" => $newStatus,
                ));
            }

            flock($fp, LOCK_UN);
        } else {
            $error = true;
        }

        fclose($fp);

        try {
            FileManager::chmod(Yii::app()->params["systemStatusLock"], 0666);
        } catch (Exception $e) {
            // pass
        }

        if ($error) {
            throw new Exception("Error changing system status.");
        }
    }

    /**
     * Check if git inited
     * @return bool
     */
    public static function gitInited() {
        if (is_dir(Yii::app()->params["packages"]["path"]["scripts"] . DS . ".git")) {
            return true;
        }

        return false;
    }

    /**
     * Check if git configured
     * @return bool
     */
    public static function gitConfigured() {
        $system = System::model()->findByPk(1);

        if (!$system->git_url) {
            return false;
        }

        if ($system->git_proto == System::GIT_PROTO_HTTPS && (!$system->git_username || !$system->git_password)) {
            return false;
        }

        if ($system->git_proto == System::GIT_PROTO_SSH) {
            $keyPath = Yii::app()->params["system"]["filesPath"] . DS . Yii::app()->params["packages"]["git"]["key"];

            if (!file_exists($keyPath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update git status
     * @param $status
     * @throws Exception
     */
    public static function updateGitStatus($status) {
        $system = System::model()->getByPk(1);

        $notIdle = array(
            System::GIT_STATUS_INIT,
            System::GIT_STATUS_CONFIG,
            System::GIT_STATUS_FAILED,
            System::GIT_STATUS_SYNC
        );

        if ($status == $system->git_status) {
            return;
        }

        if (in_array($status, $notIdle) && in_array($system->git_status, $notIdle)) {
            throw new Exception("Permission denied.");
        }

        $system->git_status = $status;
        $system->save();
    }
} 