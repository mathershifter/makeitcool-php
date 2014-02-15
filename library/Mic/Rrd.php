<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * MC PHP Framework
 *
 * PHP version 5.x
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  MC
 * @package   MC_Rrd
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: Rrd.php 1461 2011-02-02 19:17:33Z jmather5 $
 * @filesource
 */

/**
 * @see MC_Loader
 */
require_once 'MC/Loader.php';

/**
 * @see MC_Object 
 */
require_once 'MC/Object.php';

/**
 * @see MC_Array 
 */
require_once 'MC/Array.php';

/**
 * Rrd wrapper class
 *
 * Wraps rrdtool in a simple-to-use php object.  Allows adapters to be wrtten
 * for different access methods. So far there is: local proc, SSH, and TCP
 * (rrdsrv).
 * Please see the RRDTool
 * {@link http://oss.oetiker.ch/rrdtool/doc/rrdtool.en.html website} for 
 * rrdtool specfic details.
 *
 * @tutorial MC/Rrd/Rrd.pkg
 *
 * @category  MC
 * @package   MC_Rrd
 */
class MC_Rrd extends MC_Object
{
    /**#@+
     * Error codes
     */
    const RRDTOOL_OPEN_ERROR   = 0;
    const RRDTOOL_CALL_ERROR   = 1;
    const RRDTOOL_CLOSED_ERROR = 2;
    const RRDTOOL_PARSE_ERROR  = 3;
    const RRDTOOL_LOAD_ERROR   = 4;
    /**#@-*/
    
    /**#@+
     * Settable properties
     *
     * @access public
     */
    public $parseResponse = true;
    public $defaultAdapter = 'proc';
    /**#@-*/
    
    /**
     * Reference to MC_Rrd_Adapter_* object
     *
     * @access protected
     */
    protected $rrdtool;
    
    /**
     * Holds the most recent response data
     *
     * @access private
     */
    private $_response;
    
    /**
     *
     * @access private
     */
    private $_metadata = null;
    
    /**
     * 
     */
    private $_data = null;
    
    /**
     * Create a new MC_Rrd object.  If a uri is passed, open rrdtool immediately
     *
     * @param string $uri     a valid URI
     * @param array  $options any options to pass to the open method
     * @access public
     */
    public function __construct($uri=false, $options=array())
    {
        if ($uri) {
            $this->open($uri, $options);
        }
    }
    
    /**
     * Sends a command to rrdtool
     *
     * @param string $command the command
     * @param array  $args    parameters for rrdtool command
     * @throws Exception on error throws an Exception with an error code
     * @return MC_Rrd
     */
    public function __call($command, $args=array())
    {
        try {
            if (!is_object($this->rrdtool)) {
                require_once 'MC/Rrd/Exception.php';
                throw new MC_Rrd_Exception("No connection to rrdtool", self::RRDTOOL_CLOSED_ERROR);
            } else {
                $response = $this->rrdtool->read($command, $args);
                
                if ($this->parseResponse === true) {
                    $response = $this->_parse($command, $response);
                }
                
                $this->_storeResponse($response);
            } 
        } catch(Exception $e) {
            throw new MC_Rrd_Exception($e->getMessage(), self::RRDTOOL_CALL_ERROR);
        }
        
        return $this;
    } 
     
    /**
     * Parse the rrdtool response
     *
     * ... using a MC_Rrd_Parser_* class
     *
     * @param $command
     * @param $response
     * @throws Exception on error throws an Exception with an error code
     * @return MC_Rrd
     */
    private function _parse($command, $response)
    {
        $parserClass = join('_', array(__CLASS__, 'Parser', ucfirst($command)));
        
        try {
            require_once 'MC/Loader.php';
            MC_Loader::loadClass($parserClass);
        } catch (Exception $e) {
            /* ignore error and return */
            return $response;
        }
        
        // this is unexpected, throw an exception
        if (!method_exists($parserClass, 'parse')) {
            require_once 'MC/Rrd/Exception.php';
            throw new MC_Rrd_Exception("Expected $parserClass to define parse",
                                        self::RRDTOOL_PARSE_ERROR); 
        }
        
        return call_user_func(array($parserClass, 'parse'), $response);
    }
    
    /**
     * Close rrdtool as defined by the appropriate adapter class
     */
    public function close() {
        // to close, simply invoke the destructor for the rrdtool object
        $this->rrdtool = null;
    }
    
    /**
     * Locate the appropriate adapter class and open the rrdtool resource
     *
     * @param string $command the command
     * @param array  $args    parameters for rrdtool command
     * @throws Exception on error throws an Exception with an error code
     * @return MC_Rrd
     */
    public function open($uri, $options=array())
    {
        // check for invalid uri and be nice to the user by adding the default
        if (!preg_match('/^\w+\:\/\//', $uri)) {
            $uri .= "file://{$uri}";
        }
        
        $parsedUri    = new MC_Array(parse_url($uri));
        $options      = new MC_Array($options);
        $adapterClass = join('_', array(__CLASS__, 'Adapter', ucfirst($parsedUri->scheme)));
        
        /*
         * attempt to dynamically load the matching adapter
         */
        try {
            MC_Loader::loadClass($adapterClass);
        } catch (Exception $e) {
            require_once 'MC/Rrd/Exception.php';
            throw new MC_Rrd_Exception("Failed to load adapter: {$parsedUri->scheme}", self::RRDTOOL_LOAD_ERROR);
        }
        
        try {
            $this->rrdtool = new $adapterClass($parsedUri, $options);
        } catch(Exception $e) {
            require_once 'MC/Rrd/Exception.php';
            throw new MC_Rrd_Exception($e->getMessage(), self::RRDTOOL_OPEN_ERROR);
        }
        
        return $this;    
    }
    
    /**
     * Sort and store the rrdtool responses
     */
    private function _storeResponse($response)
    {
        //$response = is_array($response) ? new MC_Array($response) : $response;
        if (is_array($response)) {
            $response = A($response);
            
            if ($response->hasKey('metadata')) {
                $this->_setMetaData($response->metadata); 
            }    
            
            if ($response->hasKey('data')) {
                $this->_setData($response->data);     
            }
        }
        
        $this->_setResponse($response);
    }
    
    protected function _setMetadata($data)
    {
        $this->_metadata = is_array($data) ? new MC_Array($data) : null;
    }
    
    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * 
     */
    private function _setData($data)
    {
        $this->_data = $data;
    }
    
    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
    /**
     * 
     */
    public function _setResponse($response)
    {
        $this->_response = $response;
    }
    
    /**
     * get the response data only, backward compatible
     *
     * @return mixed
     */
    public function response()
    {
        return $this->getData() ? $this->getData() : $this->getResponse();
    }
}
