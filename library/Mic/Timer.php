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
 * @package   MC_Timer
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @filesource
 */

/**
 * Base class for time objects
 *
 * @category  MC
 * @package   MC_Timer
 */
class MC_Timer
{
    /**
     * 
     */
    private $_startTime;
    
    /**
     * 
     */
    private $_stopTime;
    
    /**
     * 
     */
    private $_duration;
    
    public function __construct()
    {
        
    }
    
    public function measure()
    {
        $args = func_get_args();
        
        $callback = array_shift($args);
        
        if (!is_callable($callback)) {
            throw new MC_Timer_Exception("Callback mist be callable");
        }
        
        $this->restart();
        
        $result = call_user_func_array($callback, $args);
        
        $this->stop();
        
        return $result;
    }
    
    public function getDuration()
    {
        return $this->_stopTime - $this->_startTime;
    }
    
    
    public function isRunning()
    {
        return isset($this->_starTime) === isset($this->_endTime) ? false : true;
    }
    
    public function start()
    {
        if ($this->isRunning()) {
            throw new MC_Timer_Exception("Timer is already running");
        }
        
        $this->_startTime = microtime(1);
        
    }
    
    public function restart($callback=null)
    {
        $this->_startTime = null;
        $this->_endTime = null;
        $this->start($callback);
    }
    
    
    public function stop()
    {
        $this->_stopTime = microtime(1);
        
        return $this->getDuration();
    }
}