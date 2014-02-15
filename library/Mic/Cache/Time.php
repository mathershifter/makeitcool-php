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
 * @category   MC
 * @package    MC_Cache
 * @subpackage MC_Cache_Time
 * @author     Jesse R. Mather <jrmather@gmail.com>
 * @copyright  2009 Nobody
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version    SVN: $Id: $
 * @filesource
 */

/**
 * @see MC_Array
 */
require_once 'MC/Array.php';

/**
 * MC_Cache_Time
 * 
 * @category   MC
 * @package    MC_Cache
 * @subpackage MC_Cache_Time
 */
class MC_Cache_Time
{   
    // Class constants
    const DEFAULT_LIFETIME = 86400;
    
    /**
     * Stores reference to backend object 
     * 
     * @var MC_Cache_Time_Db_Abstract private
     */
    private $_backend;
    
    /**
     * @var MC_Cache_Time private
     */
    private static $_instance;
    
    /**
     * Deferred conection parameters
     */
    private static $_deferredBackend;
    private static $_deferredOptions;
     
         
    /**
     * Connects to the Cache_Time backend
     * 
     * @param string $backend
     * @param array $options
     * @return MC_Cache_Time 
     */
    public function __construct($backend='sqlite', $options=null)
    {
        
        $backendClass = __CLASS__ . '_Db_' . ucfirst($backend);
        
        require_once 'MC/Loader.php';
        
        MC_Loader::loadClass($backendClass);
        $this->_backend = new $backendClass($options);
        
        self::$_instance = $this;
    }
    
    /**
     * Connects to the Cache_Time backend, reuses an already open connection 
     *
     * @param string $backend
     * @param array $options
     * @return MC_Cache_Time
     */
    public static function connect($backend=null, $options=null)
    {
        if (self::$_instance instanceof MC_Cache_Time)
            return self::$_instance;
    
        // get deferred parameters
        if (!$backend && isset(self::$_deferredBackend)) {
            $backend = self::$_deferredBackend;
            
            if (isset(self::$_deferredOptions)) {
                $options = self::$_deferredOptions;
            }
        }
        
        if (!$backend) {
            require_once 'MC/Cache/Time/Exception.php';
            throw new MC_Cache_Time_Exception("no backend defined.");
        }
        
        return new self($backend, $options);
    }
    
    public static function connectLater($backend=null, $options=null)
    {
        self::$_deferredBackend = $backend;
        self::$_deferredOptions = $options;
    }
    
    /**
     * Retrieve a time series of cache entries
     * 
     * @param string $key
     * @param string|integer $start
     * @param string|integer $end
     * @return array|boolean
     */
    public function getSeries($key, $start=false, $end=false)
    {
        $data = array();

        $result = $this->_backend->select($key, $this->_parseTime($start),
                                                $this->_parseTime($end)
        );
                
        if (empty($result)) return false;
        
        foreach ($result as $row) {
            $data[$row->timestamp] = $row->data;
        }
        
        return $data;
    }
    
    /**
     * Gets the latest entry
     *
     * @param string $key
     * @param string|integer $start
     * @param string|integer $end
     * @param string|array $callback
     * @param array $callbackParams
     */
    public function get($key, $start=false, $end=false, $callback=false, 
                        $callbackParams=array()
    ) {
        $result = $this->_backend->select($key, $this->_parseTime($start),
                                                $this->_parseTime($end)
        );
        
        if (empty($result)) {
            //echo __METHOD__ . " - cache missed!\n";
            if (is_callable($callback)) {
                $result = $this->set($key,
                    call_user_func_array($callback, $callbackParams)
                );
            }
            
            return $result;
        } //else { echo __METHOD__ . " - cache hit!\n"; }
        
        return empty($result) ? false : array_pop($result)->data;
    }
    
    /**
     * Creates a new cache entry, then gets it 
     *
     * @param string $key
     * @param mixed $data
     * @param integer $timestamp
     * @param integer $lifetime
     * @return array|boolean
     */
    public function set($key, $data, $timestamp=false, $lifetime=false)
    {
        if (!$key) {
            require_once 'MC/Cache/Time/Exception.php';
            throw new MC_Cache_Time_Exception("key must be set.");
        }
        
        $timestamp = $this->_parseTime($timestamp);
        
        $lifetime  = is_int($lifetime) ? $lifetime : self::DEFAULT_LIFETIME;

        
        $this->_backend->insert($key, serialize($data), $lifetime,
                                $timestamp
                                
        );
        
        // return the newly created entry
        return $this->get($key, $timestamp, $timestamp);
    }
    
    /**
     * Deletes a cache entry 
     *
     * @param string $key
     * @param integer $timestamp
     * @throws MC_Cache_Time_Exception
     */
    public function delete($key, $timestamp)
    {
        require_once 'MC/Cache/Time/Exception.php';
        throw new MC_Cache_Time_Exception("Delete not implemented.");
    }
    
    /**
     * Normalizes start and end times
     * 
     * @param string|integer $timestamp
     * @return integer
     */
    private function _parseTime($timestamp)
    {   
        if (is_string($timestamp)) {
            if (($timestamp = strtotime($timestamp)) === false) {
                require_once 'MC/Cache/Time/Exception.php';
                throw new MC_Cache_Time_Exception("Failed to parse timestamp.");
            }
        } elseif($timestamp < 0) {
            $timestamp = time() + $timestamp; 
        } elseif(!is_int($timestamp)) {
            $timestamp = time();
        }
        
        return $timestamp;
    }
}
