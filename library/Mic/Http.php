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
 * Mic_Http
 *
 * @category  Mic
 * @package   Mic_Http
 */
class Mic_Http extends Mic_Object
{
    /**
     * 
     */
    private $_handle;
    private $_body;
        
    public function __construct($url)
    {
        $this->_handle = curl_init($url);
        $this->header = true;
    }
    
    /**
     * 
     */
    public function __set($option, $value)
    {
        $this->_setCurlOption($option, $value);
    }
    
    /**
     * 
     */
    public function delete()
    {
        throw new Mic_Http_Exception("Not implemented");
    }
    
    /**
     * 
     */
    public static function open($url)
    {
        return new self($url);
    }
    
    /**
     * 
     */
    public function get()
    {
        $this->httpget = true;
        
        return new Mic_Http_Response($this->_execute(), $this);
    }
    
    /**
     * 
     */
    public function post()
    {
        throw new Mic_Http_Exception("Not implemented");
    }
    
    /**
     * 
     */
    public function put()
    {
        throw new Mic_Http_Exception("Not implemented");
    }
    
    
    /**
     * 
     */
    private function _execute()
    {
        ob_start();
        curl_exec($this->_handle);
        $body = ob_get_contents();
        ob_end_clean();
        
        return new Mic_String($body);
    }
    
    public function getCurlInfo($field)
    {
        $field = self::resolveConstant($field, 'CURLINFO_');
        
        return $field !== null ? curl_getinfo($this->_handle, $field) : null;
    }
    
    /**
     * 
     */
    private function _setCurlOption($option, $value)
    {
        $option = self::resolveConstant($option, 'CURLOPT_');
        curl_setopt($this->_handle, $option, $value);
    }
    
    /**
     * 
     */
    private function _setCurlOptions($options)
    {
        foreach ($options as $name=>$value) {
            $this->_setCurlOption($name, $value);
        }
    }
        
    /**
     * 
     */
    public static function resolveConstant($name, $prefix='')
    {   
        if (is_string($name)) {
            $prefix   = S($prefix)->underscorize()->upper();
            $name = S($name)->underscorize()->upper();
            
            $name  = ($name->match('/' . $prefix .'/i') ? '' : $prefix)
                . $name;
            
            $name = defined($name) ? constant($name) : null;
            
        }
        
        return $name;
    }
}