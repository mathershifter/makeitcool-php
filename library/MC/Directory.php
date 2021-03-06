<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * MC PHP Framework
 *
 * PHP version 5.2+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  MC
 * @package   MC_Proc
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * MC_Directory
 *
 * @category  MC
 * @package   MC_Directory
 */
class MC_Directory extends MC_Object
{
    /**
     * 
     */
    private $_resource;
    
    
    /**
     * 
     */
    private $_meta;
        
    /**
     * 
     */
    public function __get($name)
    {
        return $this->_meta->{$name};
    }
    
    /**
     * 
     */
    protected function __set($name, $value)
    {
        $this->_meta->{$name} = $value;
    }
    
    /**
     * 
     */
    public function __construct($file)
    {
        if (self::isDirectory($file)) {
            if (!$this->_resource = opendir($file)) {
                throw new MC_File_Exception("Failed to open directory '$file'");
            }
            
            $this->path      = $file;
            $this->realpath  = MC_File::realpath($file);
            $this->stat      = MC_File::stat($file);
        } else {
            throw new MC_File_Exception("'$file' is not a directory");
        }
    }
    
    /**
     * 
     */
    public static function isDirectory($file)
    {
        return is_dir($file) ? true : false;
    }
    
    /**
     * 
     */
    public static function open($file) 
    {
        return new self($file);    
    }
    
    /**
     * 
     * @param   string $filter
     * @param   mixed  ...     callback and/or sort flags
     * @return  MC_Array
     */
    public function scan()
    {
        $args = func_get_args();
        
        $filter    = null;
        $callback  = null;
        $sortFlags = null;
                         
        # consume the remaining arguments
        while (count($args)) {
            $arg = array_pop($args);
            if (is_callable($arg)) {
                $callback = $arg;
            } elseif (is_string($arg)) {
                $filter = $arg;
            } elseif (is_int($arg)) {
                $sortFlags = $arg;
            }
        }
        
        $files = new MC_Array();
        
        while (false !== ($file = readdir($this->_resource))) {
            if (S($file)->rmatch($filter)) {
                $files->push($this->path . '/' . $file);
                
                if ($callback) {
                    $callback($this->path . '/' . $file);
                }
            }
        }
        
        return $sortFlags ? $files->sort($sortFlags) : $files;
    }
}