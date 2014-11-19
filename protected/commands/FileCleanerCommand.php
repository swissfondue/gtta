<?php

/**
 * Class FileCleanerCommand
 */

class FileCleanerCommand extends ConsoleCommand {
    const LIFETIME = 3600;

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
     * List of temporary check files
     * @return array
     */
    private function _checksTmp() {
        $dir = Yii::app()->params['automation']['filesPath'];
        $targetChecks = TargetCheck::model()->findAll();
        $files = array();

        foreach ($targetChecks as $check) {
            if ($check->result_file) {
                $filePath = $dir . '/' . $check->result_file;

                if (file_exists($filePath) && self::_oldFile($filePath)) {
                    $files[] = $filePath;
                }
            }

            if ($check->target_file) {
                $filePath = $dir . '/' . $check->target_file;

                if (file_exists($filePath) && self::_oldFile($filePath)) {
                    $files[] = $filePath;
                }
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

        // `glob` may return false, if permission denied, for example
        if ($files == false) {
            $files = array();
        }

        foreach ($files as $key => $file) {
            if (!self::_oldFile($file)) {
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
            if (!self::_oldFile($file)) {
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
        $headers = array();
        $templateFiles = array();

        foreach ($templates as $template) {
            if ($template->type == ReportTemplate::TYPE_RTF) {
                if ($template->header_image_path) {
                    $headers[] = $headersDir . '/' . $template->header_image_path;
                }
            } else if ($template->type == ReportTemplate::TYPE_DOCX) {
                if ($template->file_path) {
                    $templateFiles[] = $templatesDir . '/' . $template->file_path;
                }
            }
        }

        $data = array();
        $files = glob($headersDir . '/*');

        if (!$files) {
            $files = array();
        }

        $data['headers'] = array_diff($files, $headers);
        $files = glob($templatesDir . '/*');

        if (!$files) {
            $files = array();
        }

        $data['templates'] = array_diff($files, $templateFiles);

        return $data;
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
            if (!self::_oldFile($file)) {
                unset($files[$key]);
            }
        }

        return $files;
    }

    /**
     * List of old temporary logos & unused client logos
     */
    private function _logos() {
        return array_merge(self::_tmpLogos(), self::_unusedLogos());
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
    private function _clean($files) {
        foreach ($files as $category) {
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
                $files = array();

                $reportTemplates = self::_reportTemplates();
                $files['automation'] = self::_checksTmp();
                $files['report_tmp'] = self::_reportTmp();
                $files['backups'] = self::_backups();
                $files['attachments'] = self::_attachments();
                $files['headers'] = $reportTemplates['headers'];
                $files['templates'] = $reportTemplates['templates'];
                $files['logos'] = self::_logos();
                $files['rating_images'] = self::_ratingImages();

                $this->_clean($files);
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, "console");
            }

            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }
}