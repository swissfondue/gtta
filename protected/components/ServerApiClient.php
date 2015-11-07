<?php

/**
 * Server API client class
 */
class ServerApiClient {
    const URL = "api/workstation";
    const TIMEOUT = 3600;
    const WORKSTATION_ID_HEADER = "X_WORKSTATION_ID";
    const WORKSTATION_KEY_HEADER = "X_WORKSTATION_KEY";

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
     * @param string|null $destination
     * @return string response
     */
    private function _sendRequest($url, $postFields=null, $destination=null, $contentType="application/json") {
        $curl = curl_init();
        $outFile = null;
        $url = sprintf("%s/%s/%s", Yii::app()->params["api"]["url"], self::URL, $url);

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => array(
                self::WORKSTATION_ID_HEADER . ": " . $this->id,
                self::WORKSTATION_KEY_HEADER . ": " . $this->key,
                "Content-Type: " . $contentType,
            ),
        );

        if ($destination !== null) {
            if (file_exists($destination)) {
                @unlink($destination);
            }

            $outFile = fopen($destination, "wb");

            if ($outFile === false) {
                throw new Exception("Unable to open file for writing: $destination");
            }

            $options[CURLOPT_RETURNTRANSFER] = false;
            $options[CURLOPT_FILE] = $outFile;
        }

        if ($postFields !== null) {
            if ($contentType == "application/json") {
                $postFields = json_encode($postFields);
            }

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
            throw new Exception("Server API error: " . $code . ", url = " . $url);
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
     * Register
     * @param $version
     * @return mixed response
     */
    public function register($version) {
        $os = "vmware";
        $product = "gtta";

        if (@file_exists(Yii::app()->params["os"]["type"])) {
            $os = @trim(@file_get_contents(Yii::app()->params["os"]["type"]));
        }

        $data = implode(":", array($product, $version, $os));
        $padding = 16 - (strlen($data) % 16);
        $data .= str_repeat(chr($padding), $padding);
        $iv = openssl_random_pseudo_bytes(16);

        $token = mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            Yii::app()->params["api"]["regKey"],
            $data,
            MCRYPT_MODE_CBC,
            $iv
        );

        $response = $this->_sendRequest("register", array(
            "product" => $product,
            "version" => $version,
            "token" => base64_encode($iv . $token),
        ));

        return $this->_parseResponse($response);
    }

    /**
     * Set status
     * @param $version
     * @param $integrationKey
     * @return mixed response
     */
    public function setStatus($version, $integrationKey) {
        $response = $this->_sendRequest("status", array(
            "version" => $version,
            "integration_key" => $integrationKey,
        ));

        return $this->_parseResponse($response);
    }

    /**
     * Get update archive and save it to the file specified
     * @param $pathToSave
     */
    public function getUpdateArchive($pathToSave) {
        $this->_sendRequest("update", array("file" => "zip"), $pathToSave);
    }

    /**
     * Get update signature and save it to the file specified
     * @param $pathToSave
     */
    public function getUpdateSignature($pathToSave) {
        $this->_sendRequest("update", array("file" => "sig"), $pathToSave);
    }
}