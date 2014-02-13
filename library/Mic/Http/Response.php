<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Mic PHP Framework
 *
 * PHP version 5.2+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  Mic
 * @package   Mic_Http
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * @see Mic_Object
 */
require_once 'Mic/Object.php';

/**
 * Mic_Http_Response
 *
 * @category  Mic
 * @package   Mic_Http_Response
 */
class Mic_Http_Response extends Mic_Object
{
    /**
     * 
     */
    private $_data;
    private $_http;
    
    /**
     * 
     */
    private $_header;
        
    public function __construct($response, $http)
    {
        $this->_data = new Mic_Array();
        $this->_http = $http;
        $this->_handleResponse($response);
    }
    
    public function __set($field, $value)
    {
        $field = S($field)->camelize();
        $this->_data["$field"] = $value;
    }
    
    public function __get($field)
    {
        if ($this->_data[$field]) {
            return $this->_data[$field];
        } else {
            // try to find the appropriate cURL field
            return $this->_http->getCurlInfo($field);
        }
    }
    
    private function _handleResponse($response)
    {   
        // store the raw response
        $this->response = "$response";
        
        list($this->header, $this->body) = $response->rsplit('/\r\n\r\n/');
        
        foreach (S($this->header)->rsplit("/\r\n/") as $line) {
            $line = S($line)->trim();    
            
            if ($matches = $line->rmatch('/^HTTP\/(?<version>[0-9\.]+)\s+(?<status>[0-9]+)\s+(?<text>[[:alnum:]\s]+)$/')) {
                $this->version    = $matches->version;
                $this->statusText = $matches->text;
                $this->status     = $matches->status;          
            } elseif ($matches = $line->rmatch('/(?<field>[[:alnum:]\-]+)\:\s+(?<value>.*)$/')) {
                
                $this->{$matches->field} = $matches->value;
            } elseif ($line->rmatch('/^$/')) {
                break;
            }
        }     
        
        
    }
}