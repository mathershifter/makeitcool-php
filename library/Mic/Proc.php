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
 * Mic_Proc
 *
 * @category  Mic
 * @package   Mic_Proc
 */
class Mic_Proc extends Mic_Object 
{
    /**
     * 
     */
    private $_code;
    
    /**
     * 
     */
    private $_descriptors = array(
        'stdin'  => array("pipe", "r"),
        'stdout' => array("pipe", "w"),
        'stderr' => array("pipe", "w")
    );
    
    /**
     * 
     */
    private $_env = array();
    
    /**
     * 
     */
    private $_pipes = array();
    
    /**
     * 
     */
    private $_proc;
    
    /**
     * 
     */
    public function __construct($command, array $descriptors=array(), $cwd=null, array $env=array())
    {
        $pipes              = array();
        $this->_descriptors = array_merge($this->_descriptors, $descriptors);
        $this->_env         = array_merge($this->_env, $env);
        
        $this->_proc        = proc_open($command, array_values($this->_descriptors), $pipes, $cwd, $this->_env);
        
        if (!is_resource($this->_proc)) {
            throw new Mic_Proc_Exception("Failed to create process");
        }
        
        foreach (array_keys($this->_descriptors) as $index=>$name) {
            if (isset($pipes[$index]) && is_resource($pipes[$index])) {
                $this->_pipes[$name] = new Mic_Proc_Resource($pipes[$index]);    
            }
             
        }
    }
    
    /**
     * 
     */
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * 
     */
    public function __get($name)
    {
        return $this->_pipes[$name];
    }
    
    /**
     * 
     */
    public function close()
    {
        foreach ($this->_pipes as $pipe) {
            unset($pipe);
        }
        
        $this->_code = is_resource($this->_proc) ? proc_close($this->_proc) : null;
        
        return $this->_code;
    }
    
    /**
     * 
     */
    public static function exec($command)
    {
        $proc = self::open($command);
        
        return "{$proc->stdout}";
    }
    
    /**
     * 
     */
    public function kill($signal=15) { return $this->terminate($signal); }
    
    /**
     * 
     */
    public function nice($priority)
    {
        proc_nice($increment);
        
        return $this;
    }
    
    /**
     * 
     */
    public static function open($command, $descriptors=array(), $cwd=null, $env=array())
    {
        return new self($command, $descriptors, $cwd, $env);
    }
    
    /**
     * 
     */
    public function renice($priority) { return $this->nice($priority); }
    
    /**
     * 
     */
    public function status()
    {
        return proc_get_status($this->_proc);
    }
    
    /**
     * 
     */
    public function terminate($signal=15)
    {
        proc_terminate($this->_proc, $signal);
        
        return $this;
    }
    
    
}