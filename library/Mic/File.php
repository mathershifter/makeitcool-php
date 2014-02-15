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
 * MC_File
 *
 * @category  MC
 * @package   MC_File
 */
class MC_File extends MC_Resource
{
    /**
     * 
     */
    private $_meta = array(
        'basename'  => null,
        'dirname'   => null,
        'extension' => null,
        'mode'      => null,
        'path'      => null,
        'realpath'  => null,
        'stat'      => null
    );
    
    /**
     * 
     */
    public function __get($name)
    {
        return $this->_meta[$name];
    }
    
    /**
     * 
     */
    protected function __set($name, $value)
    {
        $this->_meta[$name] = $value;
    }
    
    /**
     * 
     */
    protected function _init($args)
    {
        /*
         * extact file name and mode from args
         */
        $file     = array_shift($args);
        $mode     = !empty($args) ? array_shift($args) : 'r';

        $resource = @fopen($file, $mode);
        
        if (!is_resource($resource)) {
            throw new MC_File_Exception("Failed to open file: $file");
        }
        
        $this->basename  = self::basename($file);
        $this->dirname   = self::dirname($file);
        $this->extension = self::extension($file);
        $this->filename  = self::filename($file);
        $this->mode      = $mode;
        $this->path      = $file;
        $this->realpath  = self::realpath($file);
        $this->stat      = self::stat($file);
        
        return $resource;
    }
    
    /**
     * 
     */
    public static function basename($file, $suffix=null)
    {
        return basename($file, $suffix);
    }
    
    
    /**
     * 
     */
    public function close()
    {
        //unset($this->_resource);
    }
    
    /**
     * 
     */
    public static function dirname($file)
    {
        return dirname($file);
    }
    
    /**
     * 
     */
    public static function exists($file)
    {
        return file_exists($file);
    }
    
    /**
     * 
     */
    public static function extension($file)
    {
        return S(basename($file))->split('.')->pop();
    }
    
    /**
     * 
     */
    public static function filename($file)
    {
        return self::basename($file, '.' . self::extension($file));
    } 
    /**
     * 
     */
    public static function open($file, $mode='r')
    {
        return new self($file, $mode);
    }
    
    /**
     * 
     */
    public static function path($file)
    {
        return $file;
    }
    
    /**
     * 
     */
    public static function realpath($file)
    {
        return realpath($file);
    }
        
    /**
     * 
     */
    public static function permissions($file, $format='octal')
    {
        if (!self::exists($file)) {
            throw new MC_File_Exception("File '$file' does not exist.");
        }
        
        $perms = fileperms($file);
        
        if ($format == 'octal') {
            $perms = substr(sprintf("%o", $perms), -4);    
        } elseif ($format == 'string') {
            $_string = '';
        
            if (($perms & 0xC000) == 0xC000) {
                // Socket
                $_string = 's';
            } elseif (($perms & 0xA000) == 0xA000) {
                // Symbolic Link
                $_string = 'l';
            } elseif (($perms & 0x8000) == 0x8000) {
                // Regular
                $_string = '-';
            } elseif (($perms & 0x6000) == 0x6000) {
                // Block special
                $_string = 'b';
            } elseif (($perms & 0x4000) == 0x4000) {
                // Directory
                $_string = 'd';
            } elseif (($perms & 0x2000) == 0x2000) {
                // Character special
                $_string = 'c';
            } elseif (($perms & 0x1000) == 0x1000) {
                // FIFO pipe
                $_string = 'p';
            } else {
                // Unknown
                $_string = 'u';
            }
            
            // Owner
            $_string .= (($perms & 0x0100) ? 'r' : '-');
            $_string .= (($perms & 0x0080) ? 'w' : '-');
            $_string .= (($perms & 0x0040) ?
                        (($perms & 0x0800) ? 's' : 'x' ) :
                        (($perms & 0x0800) ? 'S' : '-'));
            
            // Group
            $_string .= (($perms & 0x0020) ? 'r' : '-');
            $_string .= (($perms & 0x0010) ? 'w' : '-');
            $_string .= (($perms & 0x0008) ?
                        (($perms & 0x0400) ? 's' : 'x' ) :
                        (($perms & 0x0400) ? 'S' : '-'));
            
            // World
            $_string .= (($perms & 0x0004) ? 'r' : '-');
            $_string .= (($perms & 0x0002) ? 'w' : '-');
            $_string .= (($perms & 0x0001) ?
                        (($perms & 0x0200) ? 't' : 'x' ) :
                        (($perms & 0x0200) ? 'T' : '-'));
        
            $perms = $_string;                
        }
        
        return $perms;
    }
    
    /**
     * 
     */
    public static function readable($file)
    {
        return self::exists($file) && is_readable($file);
    }
    
    /**
     * 
     */
    public static function stat($file)
    {
         return new MC_File_Stat($file);
    }
}