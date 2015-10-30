<?php

/**
 * Community API client class
 */
class CommunityApiClient {
    const URL = "api";
    const TIMEOUT = 3600;
    const INTEGRATION_KEY_HEADER = "X_INTEGRATION_KEY";
    const INITIAL_KEY_HEADER = "X_INITIAL";
    const STATUS_UNVERIFIED = 0;

    private $key;

    /**
     * Flag for the first run
     * @var bool
     */
    private $_initial = false;

    /**
     * Constructor
     * @param $key
     */
    public function __construct($key=null) {
        $this->key = $key;

        if (!$key) {
            $this->_initial = true;
        }
    }

    /**
     * Send API request
     * @param string $url
     * @param array|null $postFields
     * @param string|null $destFilePath
     * @param string $contentType
     * @return string response
     * @throws Exception
     */
    private function _sendRequest($url, $postFields=null, $destFilePath=null, $contentType="application/json") {
        $curl = curl_init();
        $outFile = null;

        $headers = array(
            "Content-Type: " . $contentType
        );

        $headers[] = $this->_initial ? self::INITIAL_KEY_HEADER . ": " . true : self::INTEGRATION_KEY_HEADER . ": " . $this->key;
        $options = array(
            CURLOPT_URL => sprintf("%s/%s/%s", Yii::app()->params["community"]["url"], self::URL, $url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => $headers,
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
            throw new Exception("Error connecting to community server: " . curl_error($curl));
        }

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($code != 200) {
            throw new Exception("Community API error: " . $code);
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
     * Status command
     * @return mixed response
     */
    public function status() {
        $response = $this->_sendRequest("status");
        return $this->_parseResponse($response);
    }

    /**
     * Get package info
     * @param $package
     * @return mixed response
     */
    public function getPackage($package) {
        $response = $this->_sendRequest("package/$package");
        return $this->_parseResponse($response);
    }

    /**
     * Get check info
     * @param $check
     * @return mixed response
     */
    public function getCheck($check) {
        $response = $this->_sendRequest("check/$check");
        return $this->_parseResponse($response);
    }

    /**
     * Get reference info
     * @param $reference
     * @return mixed response
     */
    public function getReference($reference) {
        $response = $this->_sendRequest("reference/$reference");
        return $this->_parseResponse($response);
    }

    /**
     * Get category info
     * @param $category
     * @return mixed response
     */
    public function getCategory($category) {
        $response = $this->_sendRequest("category/$category");
        return $this->_parseResponse($response);
    }

    /**
     * Get control info
     * @param $control
     * @return mixed response
     */
    public function getControl($control) {
        $response = $this->_sendRequest("control/$control");
        return $this->_parseResponse($response);
    }

    /**
     * Share reference
     * @param $reference
     * @return mixed response
     */
    public function shareReference($reference) {
        $response = $this->_sendRequest("reference", json_encode($reference));
        return $this->_parseResponse($response);
    }

    /**
     * Share category
     * @param $category
     * @return mixed response
     */
    public function shareCategory($category) {
        $response = $this->_sendRequest("category", json_encode($category));
        return $this->_parseResponse($response);
    }

    /**
     * Share control
     * @param $control
     * @return mixed response
     */
    public function shareControl($control) {
        $response = $this->_sendRequest("control", json_encode($control));
        return $this->_parseResponse($response);
    }

    /**
     * Share check info
     * @param $check
     * @return mixed response
     */
    public function shareCheck($check) {
        $response = $this->_sendRequest("check", json_encode($check));
        return $this->_parseResponse($response);
    }

    /**
     * Get package archive and save it to the file specified
     * @param $package
     * @param $pathToSave
     */
    public function getPackageArchive($package, $pathToSave) {
        $this->_sendRequest("package/$package/download", null, $pathToSave);
    }

    /**
     * Share package
     * @param $path
     * @return mixed response
     */
    public function sharePackage($path) {
        $response = $this->_sendRequest(
            "package",
            array(
                "package" => "@" . realpath($path) . ";filename=package.zip;type=application/zip"
            ),
            null,
            "multipart/form-data"
        );

        return $this->_parseResponse($response);
    }

    /**
     * Finish command
     * @param $data
     */
    public function finish($data) {
        $this->_sendRequest("finish", json_encode($data));
    }
}