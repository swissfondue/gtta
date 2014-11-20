<?php

/**
 * Class FileCleanerCommand
 */

class FileCleanerCommand extends ConsoleCommand {
    const LIFETIME = 1;

    /**
     * Check if file is old
     * @param $file
     * @return bool
     */
    private function _oldFile($file) {
        if ((time() - filectime($file)) > self::LIFETIME) {
            return true;
        }

        return false;
    }

    /**
     * List of temporary check files (input, target, result files)
     * @return array
     */
    private function _checksTmp() {
        $dir = Yii::app()->params['automation']['filesPath'];
        $dirEntries = glob($dir . '/*');

        // `glob` may return false, if permission denied, for example
        if ($dirEntries == false) {
            $dirEntries = array();
        }

        $files = array();

        // exclude dirs & new files
        foreach ($dirEntries as $key => $entry) {
            if (!is_dir($entry) && $this->_oldFile($entry)) {
                $files[] = $entry;
            }
        }

        return $files;
    }

    /**
     * List of temporary report files
     * @return array
     */
    private function _reportTmp() {
        $files = glob(Yii::app()->params['reports']['tmpFilesPath'] . '/*');

        if ($files == false) {
            $files = array();
        }

        foreach ($files as $key => $file) {
            if (!$this->_oldFile($file)) {
                unset($files[$key]);
            }
        }

        return $files;
    }

    /**
     * List of backups
     * @return array
     */
    private function _backups() {
        $files = glob(Yii::app()->params['backups']['tmpFilesPath'] . '/*');

        if ($files == false) {
            $files = array();
        }

        foreach ($files as $key => $file) {
            if (!$this->_oldFile($file)) {
                unset($files[$key]);
            }
        }

        return $files;
    }

    /**
     * List of unused attachments
     * @return array
     */
    private function _attachments() {
        $dir = Yii::app()->params['attachments']['path'];

        $tca = TargetCheckAttachment::model()->findAll(array( 'select' => 'path' ));
        $tcca = TargetCustomCheckAttachment::model()->findAll(array( 'select' => 'path' ));
        $pgta = ProjectGtCheckAttachment::model()->findAll(array( 'select' => 'path' ));

        $attachments = array();

        foreach ($tca as $attachment) {
            $attachments[] = $dir . '/' . $attachment->path;
        }

        foreach ($tcca as $attachment) {
            $attachments[] = $dir . '/' . $attachment->path;
        }

        foreach ($pgta as $attachment) {
            $attachments[] = $dir . '/' . $attachment->path;
        }

        $files = glob($dir . '/*');

        if ($files == false) {
            $files = array();
        }

        return array_diff($files, $attachments);
    }

    /**
     * List of unused report template's header images & template files
     * @return array
     */
    private function _reportTemplates() {
        $headersDir = Yii::app()->params['reports']['headerImages']['path'];
        $templatesDir = Yii::app()->params['reports']['file']['path'];

        $templates = ReportTemplate::model()->findAll();
        $paths = array();

        foreach ($templates as $template) {
            if ($template->type == ReportTemplate::TYPE_RTF) {
                if ($template->header_image_path) {
                    $paths[] = $headersDir . '/' . $template->header_image_path;
                }
            } else if ($template->type == ReportTemplate::TYPE_DOCX) {
                if ($template->file_path) {
                    $paths[] = $templatesDir . '/' . $template->file_path;
                }
            }
        }

        $unused = array();
        $headers = glob($headersDir . '/*');

        if (!$headers) {
            $headers = array();
        }

        $docs = glob($templatesDir . '/*');

        if (!$docs) {
            $docs = array();
        }

        $files = array_merge($headers, $docs);
        $unused = array_diff($files, $paths);

        return $unused;
    }

    /**
     * List of unused client logos
     * @return array
     */
    private function _unusedLogos() {
        $dir = Yii::app()->params['clientLogos']['path'];
        $clients = Client::model()->findAll();
        $logos = array();

        foreach ($clients as $client) {
            $logos[] = $dir . '/' . $client->logo_path;
        }

        // excluding system logo file
        $logos[] = Yii::app()->params["systemLogo"]["path"];

        $files = glob($dir . '/*');

        if ($files == false) {
            $files = array();
        }

        return array_diff($files, $logos);
    }

    /**
     * List of temporary client logos
     * @return array
     */
    private function _tmpLogos() {
        $files = glob(Yii::app()->params['clientLogos']['tmpFilesPath'] . '/*');

        if ($files == false) {
            $files = array();
        }

        foreach ($files as $key => $file) {
            if (!$this->_oldFile($file)) {
                unset($files[$key]);
            }
        }

        return $files;
    }

    /**
     * List of old temporary logos & unused client logos
     */
    private function _logos() {
        return array_merge($this->_tmpLogos(), $this->_unusedLogos());
    }

    /**
     * List of unused rating images
     * @return array
     */
    private function _ratingImages() {
        $dir = Yii::app()->params['reports']['ratingImages']['path'];

        $ratingImage = ReportTemplateRatingImage::model()->findAll();
        $images = array();

        foreach ($ratingImage as $image) {
            $images[] = $dir . '/' . $image->path;
        }

        $files = glob($dir . '/*');

        if (!$files) {
            $files = array();
        }

        return array_diff($files, $images);
    }

    /**
     * Remove files
     * @param $files
     */
    private function _clean() {
        $files = func_get_args();

        foreach ($files as $category) {
            error_log(print_r($category, 1));
            foreach ($category as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Run the command
     * @param array $args
     */
    public function run($args) {
        $fp = fopen(Yii::app()->params["filecleaner"]["lockFile"], "w");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            try {
                $this->_clean(
                    $this->_checksTmp(),
                    $this->_reportTmp(),
                    $this->_backups(),
                    $this->_attachments(),
                    $this->_reportTemplates(),
                    $this->_logos(),
                    $this->_ratingImages()
                );
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
            }

            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }
}