<?php

/**
 * Unlink report on exit
 */
function unlinkReportOnExit($path) {
    FileManager::unlink($path);
}

/**
 * Report plugin class
 */
abstract class ReportPlugin {
    /**
     * @var boolean report generated
     */
    protected $_generated = false;

    /**
     * @var ReportTemplate template
     */
    protected $_template;

    /**
     * @var array checks data
     */
    protected $_data;

    /**
     * @var string file name
     */
    protected $_fileName = null;

    /**
     * @var string file path
     */
    protected $_filePath = null;

    /**
     * @var int language id
     */
    protected $_language = null;

    /**
     * Constructor
     * @param ReportTemplate $template
     * @param array $data
     */
    public function __construct(ReportTemplate $template=null, $data=array(), $language=null) {
        $this->_template = $template;
        $this->_data = $data;
        $this->_language = $language;
    }

    /**
     * Get plugin for given type
     * @param ReportTemplate $template
     * @param $data
     * @param $language
     * @return ReportPlugin
     * @throws Exception
     */
    public static function getPlugin(ReportTemplate $template, $data, $language) {
        $plugins = array(
            ReportTemplate::TYPE_RTF => "RtfReport",
            ReportTemplate::TYPE_DOCX => "DocxReport",
        );

        if (!array_key_exists($template->type, $plugins)) {
            throw new Exception(Yii::t("app", "Invalid report type: {type}.", array("{type}" => $template->type)));
        }

        $plugin = $plugins[$template->type];

        return new $plugin($template, $data, $language);
    }

    /**
     * Generate report
     */
    abstract public function generate();

    /**
     * Send report over HTTP
     * @param bool $unlink
     * @param bool $exit
     * @throws Exception
     */
    public function sendOverHttp($unlink=true, $exit=true) {
        if (!$this->_generated) {
            throw new Exception(Yii::t("app", "Report not generated yet to send it over Http."));
        }

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $this->_fileName . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . filesize($this->_filePath));
        ob_clean();
        flush();

        if ($unlink) {
            @ignore_user_abort(true);
            @register_shutdown_function("unlinkReportOnExit", $this->_filePath);
        }

        readfile($this->_filePath);

        if ($unlink) {
            FileManager::unlink($this->_filePath);
        }

        if ($exit) {
            exit();
        }
    }
}