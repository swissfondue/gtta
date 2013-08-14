<?php

/**
 * API client class
 */
class ApiClient {
    const URL = "http://gtta-server.local/api/status";
    const TIMEOUT = 10;
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
     * @param array|null $postFields
     * @return string response
     */
    private function _sendRequest($postFields = null) {
        $curl = curl_init();

        $options = array(
            CURLOPT_URL => self::URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => array(
                self::WORKSTATION_ID_HEADER . ": " . $this->id,
                self::WORKSTATION_KEY_HEADER . ": " . $this->key,
            ),
        );

        if ($postFields !== null) {
            $options += array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postFields
            );
        }

        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);

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
        $response = $this->_sendRequest(array(self::PARAM_VERSION => $version));
        return $this->_parseResponse($response);
    }
}