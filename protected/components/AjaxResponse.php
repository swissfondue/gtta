<?php

/**
 * Ajax response class. 
 */
class AjaxResponse
{    
    /**
     * @var string successfull status.
     */
    const STATUS_OK = 'ok';
    
    /**
     * @var string response error.
     */
    const STATUS_ERROR = 'error';
    
    /**
     * @var string response status.
     */
    private $_status = self::STATUS_OK;
    
    /**
     * @var string error text.
     */
    private $_errorText = null;
    
    /**
     * @var mixed response data. 
     */
    private $_data = array();
    
    /**
     * Set status to error. 
     */
    public function setError($errorText = 'Your request is invalid.')
    {
        $this->_status    = self::STATUS_ERROR;
        $this->_errorText = $errorText;
    }
    
    /**
     * Add data to response. 
     */
    public function addData($key, $value)
    {
        $this->_data[$key] = $value;
    }
    
    /**
     * Serialize response into JSON object.
     * @return string json string. 
     */
    public function serialize()
    {
        $object = array(
            'status' => $this->_status,
            'csrf'   => Yii::app()->request->csrfToken
        );
        
        if ($this->_status == self::STATUS_ERROR)
            $object['errorText'] = $this->_errorText;
        
        if (count($this->_data) > 0)
            $object['data'] = array();
        
        foreach ($this->_data as $k => $v)
            $object['data'][$k] = $v;
            
        return json_encode($object);
    }
}