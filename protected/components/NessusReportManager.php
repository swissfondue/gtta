<?php

/**
 * Class InvalidNessusReportException
 */
class InvalidNessusReportException extends Exception {}

/**
 * Class NessusReportManager
 */
class NessusReportManager {
    /**
     * XML object
     * @var null
     */
    private $_xmlObj = null;

    /**
     * @var array $ratings
     */
    public static $ratings = [
        "Critical",
        "High",
        "Medium",
        "Low",
        "Information",
        "None"
    ];

    /**
     * Parse nessus report
     * @param $filepath
     * @throws Exception
     * @throws InvalidNessusReportException
     */
    public function parse($filepath) {
        if (!file_exists($filepath)) {
            throw new Exception("File doesn't exists.");
        }

        $data = [];

        try {
            $this->_xmlObj = simplexml_load_file($filepath);
            $report = $this->_xmlObj->Report[0];
            $name = Yii::t("app", "N/A");

            if (isset($this->_xmlObj->Report[0]["name"])) {
                $name = $this->_xmlObj->Report[0]["name"] . PHP_EOL;
            }

            $data = [
                "name" => (string) $name,
                "hosts" => $this->_parseHosts($report->ReportHost)
            ];
        } catch (Exception $e) {
            throw new InvalidNessusReportException();
        }

        return $data;
    }

    /**
     * Parse report's hosts
     * @param $reportHost
     * @return array
     */
    private function _parseHosts($reportHost) {
        $hosts = [];

        foreach ($reportHost as $host) {
            $vulns = $host->ReportItem;

            $hosts[] = [
                "name" => trim((string) $host["name"]),
                "properties" => $this->_parseHostProperties($host[0]->HostProperties->children()),
                "vulnerabilities" => $this->_parseHostVulns($vulns)
            ];
        }

        return $hosts;
    }

    /**
     * Parse host's properties
     * @param $hostProperties
     * @return array
     */
    private function _parseHostProperties($hostProperties) {
        $props = [];
        $names = [
            "mac-address",
            "system-type",
            "operating-system",
            "host-ip",
            "host-fqdn",
            "netbios-name"
        ];

        foreach ($hostProperties as $hp) {
            $attrs = $hp->attributes();
            $name = (string) $attrs["name"];
            $value = (string) $hp;

            if (in_array($name, $names)) {
                $props[$name] = $value;
            }
        }

        return $props;
    }

    /**
     * Parse host vulnerabilities
     * @param $hostVulns
     * @return array
     */
    private function _parseHostVulns($hostVulns) {
        $vulns = [];

        foreach ($hostVulns as $item) {
            $cvss = $item->cvss_base_score ? (string) $item->cvss_base_score : 0.0;
            $attributes = $item->attributes();

            $vulns[] = [
                "plugin_id" => (int)$attributes["pluginID"],
                "plugin_name" => (string)$attributes["pluginName"],
                "svc_name" => (string)$attributes["svc_name"],
                "severity" => $cvss,
                "pluginFamily" => (string)$attributes["pluginFamily"],
                "description" => (string) $item->description,
                "cve" => (string) $item->cve,
                "risk_factor" => (string) $item->risk_factor,
                "see_also" => (string) $item->see_also,
                "solution" => (string) $item->solution,
                "synopsis" => (string) $item->synopsis
            ];
        }

        return $vulns;
    }
}