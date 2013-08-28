<?php

/**
 * API client class
 */
class ApiClient {
    const URL = "api";
    const TIMEOUT = 3600;
    const WORKSTATION_ID_HEADER = "X_WORKSTATION_ID";
    const WORKSTATION_KEY_HEADER = "X_WORKSTATION_KEY";
    const PARAM_VERSION = "version";

    private $id;
    private $key;

    /**
     * Constructor
     * @param $id
     * @param $key
     */
    public function __construct($id, $key) {
        $this->id = $id;
        $this->key = $key;
    }

    /**
     * Send API request
     * @param string $url
     * @param array|null $postFields
     * @param string|null $destFilePath
     * @return string response
     */
    private function _sendRequest($url, $postFields=null, $destFilePath=null) {
        $curl = curl_init();
        $outFile = null;

        $options = array(
            CURLOPT_URL => sprintf("%s/%s/%s", Yii::app()->params["api"]["url"], self::URL, $url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => array(
                self::WORKSTATION_ID_HEADER . ": " . $this->id,
                self::WORKSTATION_KEY_HEADER . ": " . $this->key,
            ),
        );

        if ($destFilePath !== null) {
            if (file_exists($destFilePath)) {
                @unlink($destFilePath);
            }

            $outFile = fopen($destFilePath, "wb");

            if ($outFile === false) {
                throw new Exception("Unable to open file for writing: $destFilePath");
            }

            $options[CURLOPT_RETURNTRANSFER] = false;
            $options[CURLOPT_FILE] = $outFile;
        }

        if ($postFields !== null) {
            $options += array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postFields
            );
        }

        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);

        if ($outFile !== false) {
            @fclose($outFile);
        }

        if ($result === false) {
            throw new Exception("Error connecting to server: " . curl_error($curl));
        }

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($code != 200) {
            throw new Exception("API error: " . $code);
        }

        return $result;
    }

    /**
     * Parse response
     * @param $response
     * @return mixed response
     */
    private function _parseResponse($response) {
        return json_decode($response);
    }

    /**
     * Set status
     * @param $id
     * @param $key
     * @param $version
     * @return mixed response
     */
    public function setStatus($version) {
        $response = $this->_sendRequest("status", array(self::PARAM_VERSION => $version));
        return $this->_parseResponse($response);
    }

    /**
     * Get update archive and save it to the file specified
     * @param $version
     * @param $pathToSave
     */
    public function getUpdateArchive($version, $pathToSave) {
        $this->_sendRequest("update/$version/zip", null, $pathToSave);
    }

    /**
     * Get update signature and save it to the file specified
     * @param $version
     * @param $pathToSave
     */
    public function getUpdateSignature($version, $pathToSave) {
        $this->_sendRequest("update/$version/sig", null, $pathToSave);
    }
}