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
 * @package   Mic_Proc
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * Mic_Resource
 *
 * @category  Mic
 * @package   Mic_Resource
 */
class Mic_Resource extends Mic_Object implements Iterator
{
   
    /**
     * 
     */
    private $_buffer = array();
    
    /**
     * 
     */
    private $_index = 0;
    
    /**
     * 
     */
    protected $_resource;
   
    /**
     * 
     */
    public function __construct()
    {
        $args = func_get_args();
        
        /**
         * Allow overriding class to process args
         */
        $resource = $this->_init($args);
        
        $this->_resource = $resource;
    }
    
    /**
     * 
     */
    public function __destruct()
    {
        if (is_resource($this->_resource)) {
            fclose($this->_resource);
        }
    }

    /**
     * 
     */
    public function __toString()
    {
        return $this->read();
    }
    
    /**
     * 
     */
    protected function _init($args)
    {
        $resource = array_shift($args);
        if (!is_resource($resource)) {
            throw new Mic_Resource_Exception("Expected a valid resource.");
        }
        
        return $resource;
    }
    
    /**
     * 
     */
    private function _readLine()
    {
        $line = false;
        
        if (($line = fgets($this->_resource)) !== false) {
            $this->_buffer[] = trim($line);
        }
        
        return $line;
    }
   
    /**
     * 
     */
    public function current()
    {
        return current($this->_buffer);
    }
    
    /**
     * 
     */
    public function key()
    {
        return key($this->_buffer);    
    }
    
    /**
     * 
     */
    public function next()
    {
        next($this->_buffer);   
        ++$this->_index;
    }
    
    /**
     * 
     */
    public function read()
    {
        // save the rest of the stream to the buffer so we can iterate it later
        $this->_buffer = array_merge($this->_buffer,
            explode("\n", trim(stream_get_contents($this->_resource))));
        
        return implode("\n", $this->_buffer);
    }
    
    /**
     * 
     */
    public function rewind()
    {
        reset($this->_buffer);
        $this->_index = 0;
    }
    
    
    /**
     * Valid actually reads the next line and pushes it onto the buffer
     */
    public function valid()
    {
        $this->_readLine();
        
        //echo "INDEX: {$this->_index} SIZE: " . count($this->_buffer) . "\n";
        
        return $this->_index < count($this->_buffer);
    }
    
    /**
     * 
     */
    public function write($data)
    {
        fwrite($this->_resource, $data);
        return $this;    
    }     
}